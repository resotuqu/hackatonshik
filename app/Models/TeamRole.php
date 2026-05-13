<?php

namespace App\Models;

use Database\Factories\TeamRoleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamRole extends Model
{
    /** @use HasFactory<TeamRoleFactory> */
    use HasFactory;

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'team_role_skills');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(TeamApplication::class);
    }

    public function scopeWhereCaptain(Builder $query, Team $team): Builder
    {
        return $query
            ->whereBelongsTo($team)
            ->where('user_id', $team->user_id);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected $fillable = [
        'title',
        'description',
        'team_id',
        'role_id',
        'user_id',
    ];
}
