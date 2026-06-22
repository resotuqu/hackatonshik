<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrganizerApplicationStatus;
use App\Enums\OrganizerEntityType;
use App\Enums\UserRole;
use Database\Factories\OrganizerApplicationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class OrganizerApplication extends Model
{
    /** @use HasFactory<OrganizerApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entity_type',
        'company_name',
        'note',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'entity_type' => OrganizerEntityType::class,
            'status' => OrganizerApplicationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, $this> */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === OrganizerApplicationStatus::Pending;
    }

    public function isRejected(): bool
    {
        return $this->status === OrganizerApplicationStatus::Rejected;
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePending($query): void
    {
        $query->where('status', OrganizerApplicationStatus::Pending);
    }

    public function approve(User $reviewer): void
    {
        DB::transaction(function () use ($reviewer): void {
            $locked = self::query()->lockForUpdate()->findOrFail($this->id);

            $locked->update([
                'status' => OrganizerApplicationStatus::Approved,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'admin_note' => null,
            ]);

            $locked->user->forceFill(['role' => UserRole::PARTNER])->save();
        });

        $this->refresh();
    }

    public function reject(User $reviewer, ?string $adminNote = null): void
    {
        $this->update([
            'status' => OrganizerApplicationStatus::Rejected,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_note' => $adminNote,
        ]);
    }

    public function resubmit(OrganizerEntityType $entityType, ?string $companyName, string $note): void
    {
        $this->update([
            'entity_type' => $entityType,
            'company_name' => $companyName,
            'note' => $note,
            'status' => OrganizerApplicationStatus::Pending,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'admin_note' => null,
        ]);
    }

    public static function createPendingForUser(
        User $user,
        OrganizerEntityType $entityType,
        ?string $companyName,
        string $note,
    ): self {
        return self::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'entity_type' => $entityType,
                'company_name' => $companyName,
                'note' => $note,
                'status' => OrganizerApplicationStatus::Pending,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'admin_note' => null,
            ],
        );
    }
}
