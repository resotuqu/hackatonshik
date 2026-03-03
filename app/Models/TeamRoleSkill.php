<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamRoleSkill extends Model
{
    /** @use HasFactory<\Database\Factories\TeamRoleSkillFactory> */
    use HasFactory;

    protected $table = 'team_role_skills';

    public function skill() {
        return $this->belongsTo(Skill::class);
    }

    public function teamRole() {
        return $this->belongsTo(TeamRole::class);
    }
}
