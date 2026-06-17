<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\HackatonAnalyticsEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HackatonAnalyticsEvent extends Model
{
    /** @use HasFactory<HackatonAnalyticsEventFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'hackaton_id',
        'user_id',
        'event_name',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Hackaton, $this> */
    public function hackaton(): BelongsTo
    {
        return $this->belongsTo(Hackaton::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
