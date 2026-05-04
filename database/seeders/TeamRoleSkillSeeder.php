<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\TeamRole;
use App\Models\TeamRoleSkill;
use Illuminate\Database\Seeder;

class TeamRoleSkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = Skill::query()->orderBy('id')->pluck('id');
        if ($skills->isEmpty()) {
            return;
        }

        $take = min(3, $skills->count());
        foreach (TeamRole::query()->orderBy('id')->get() as $teamRole) {
            foreach ($skills->shuffle()->take($take) as $skillId) {
                TeamRoleSkill::query()->firstOrCreate(
                    [
                        'team_role_id' => $teamRole->id,
                        'skill_id' => $skillId,
                    ],
                );
            }
        }
    }
}
