<?php

namespace App\Models;

use Database\Factories\TeamRoleSkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamRoleSkill extends Model
{
    /** @use HasFactory<TeamRoleSkillFactory> */
    use HasFactory;

    protected $table = 'team_role_skills';

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected $fillable = [
        'team_role_id',
        'skill_id',
    ];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function teamRole(): BelongsTo
    {
        return $this->belongsTo(TeamRole::class);
    }
}
