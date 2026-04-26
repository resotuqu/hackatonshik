<?php

namespace App\Models;

use App\Enums\HackatonStatus;
use Database\Factories\HackatonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hackaton extends Model
{
    /** @use HasFactory<HackatonFactory> */
    use HasFactory;

    private const SHOW_RELATIONS = [
        'user',
        'documents',
        'teams.roles',
        'teams.user',
        'applications.team',
        'applications.reviewer',
        'cases.fields',
        'cases.submissions.answers',
        'cases.submissions.score',
        'announcements.author',
        'announcements.images',
        'certificates.user',
        'judges',
        'images',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function teamsCount(): int
    {
        return $this->teams()->count();
    }

    public function participantsCount(): int
    {
        return $this->teams()
            ->withCount(['roles as participants_count' => fn ($query) => $query->whereNotNull('user_id')])
            ->get()
            ->sum('participants_count');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(HackatonDocument::class);
    }

    public function usersDocuments(): HasMany
    {
        return $this->hasMany(UserHackatonDocument::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(HackatonApplication::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(HackatonCase::class)->orderBy('sort_order');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(HackatonAnnouncement::class)->latest('published_at');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(HackatonCertificate::class)->latest('issued_at');
    }

    public function images(): HasMany
    {
        return $this->hasMany(HackatonImage::class)->orderBy('sort_order');
    }

    public function judgeAssignments(): HasMany
    {
        return $this->hasMany(HackatonJudge::class);
    }

    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hackaton_judges')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    public function judgeInvitations(): HasMany
    {
        return $this->hasMany(JudgeInvitation::class)->latest();
    }

    public function isJudge(User $user): bool
    {
        return $this->judges()->where('users.id', $user->id)->exists();
    }

    public function loadShowRelations(): self
    {
        $this->load(self::SHOW_RELATIONS);

        return $this;
    }

    public function syncStatusByTimeline(): bool
    {
        if ($this->status === HackatonStatus::ARCHIVED) {
            return false;
        }

        $now = now();

        $nextStatus = match (true) {
            ! $this->is_public => HackatonStatus::DRAFT,
            $now->lt($this->start_at) && $this->status === HackatonStatus::PUBLISHED => HackatonStatus::PUBLISHED,
            $now->lt($this->start_at) => HackatonStatus::REGISTRATION_OPEN,
            $now->between($this->start_at, $this->end_at) => HackatonStatus::IN_PROGRESS,
            $now->gt($this->end_at) && $this->status === HackatonStatus::JUDGING => HackatonStatus::JUDGING,
            default => HackatonStatus::FINISHED,
        };

        if ($this->status === $nextStatus) {
            return false;
        }

        $this->update(['status' => $nextStatus]);

        return true;
    }

    protected function casts(): array
    {
        return [
            'status' => HackatonStatus::class,
            'is_public' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_url',
        'start_at',
        'end_at',
        'is_public',
        'status',
    ];
}
