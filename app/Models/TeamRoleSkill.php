<?php

namespace App\Models;

use Database\Factories\TeamRoleSkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamRoleSkill extends Model
{
    /** @use HasFactory<TeamRoleSkillFactory> */
    use HasFactory;

    protected $table = 'team_role_skills';

    protected $fillable = [
        'team_role_id',
        'skill_id',
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function teamRole()
    {
        return $this->belongsTo(TeamRole::class);
    }
}
