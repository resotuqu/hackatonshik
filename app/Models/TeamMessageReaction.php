<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TeamMessageReactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMessageReaction extends Model
{
    /** @use HasFactory<TeamMessageReactionFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'team_message_id',
        'user_id',
        'emoji',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<TeamMessage, $this> */
    public function message(): BelongsTo
    {
        return $this->belongsTo(TeamMessage::class, 'team_message_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
