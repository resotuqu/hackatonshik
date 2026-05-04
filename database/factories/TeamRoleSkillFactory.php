<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\TeamRole;
use App\Models\TeamRoleSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamRoleSkill>
 */
class TeamRoleSkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_role_id' => TeamRole::factory(),
            'skill_id' => Skill::factory(),
        ];
    }
}
