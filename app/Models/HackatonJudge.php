<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JudgeDomain;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property JudgeDomain $domain
 */
class HackatonJudge extends Model
{
    use HasFactory;

    protected $fillable = [
        'hackaton_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'domain',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'domain' => JudgeDomain::class,
        ];
    }

    public function hackaton(): BelongsTo
    {
        return $this->belongsTo(Hackaton::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
