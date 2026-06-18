<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TeamMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamMessage extends Model
{
    /** @use HasFactory<TeamMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'content',
        'type',
        'parent_id',
    ];

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<TeamMessage, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(TeamMessage::class, 'parent_id');
    }

    /** @return HasMany<TeamMessageReaction, $this> */
    public function reactions(): HasMany
    {
        return $this->hasMany(TeamMessageReaction::class);
    }
}
