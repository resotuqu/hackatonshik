<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TeamSocialLink extends Model
{
    /** @use HasFactory<\Database\Factories\TeamSocialLinkFactory> */
    use HasFactory;

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    protected $fillable = [
        'team_id',
        'name',
        'url',
    ];
}
