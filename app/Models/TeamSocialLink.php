<?php

namespace App\Models;

use Database\Factories\TeamSocialLinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSocialLink extends Model
{
    /** @use HasFactory<TeamSocialLinkFactory> */
    use HasFactory;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected $fillable = [
        'team_id',
        'name',
        'url',
    ];
}
