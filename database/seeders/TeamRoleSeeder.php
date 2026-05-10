<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\Role;
use App\Models\TeamRole;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class TeamRoleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('ru_RU');
        $roles = Role::query()->orderBy('id')->take(4)->get();
        $participants = User::query()->where('role', 'user')->orderBy('id')->get();
        if ($roles->isEmpty() || $participants->isEmpty()) {
            return;
        }

        $participantIndex = 0;
        $hackatons = Hackaton::query()->with('teams')->orderBy('id')->get();
        foreach ($hackatons as $hackaton) {
            $assignedForHackaton = 0;
            $targetForHackaton = $this->targetParticipantsCount($hackaton->id);

            foreach ($hackaton->teams()->orderBy('id')->get() as $team) {
                foreach ($roles as $role) {
                    $userId = null;
                    if ($assignedForHackaton < $targetForHackaton) {
                        $userId = $participants[$participantIndex % $participants->count()]->id;
                        $participantIndex++;
                        $assignedForHackaton++;
                    }

                    TeamRole::query()->create([
                        'title' => $role->name.' в команде',
                        'description' => $faker->sentence(10),
                        'team_id' => $team->id,
                        'role_id' => $role->id,
                        'user_id' => $userId,
                    ]);
                }
            }
        }
    }

    private function targetParticipantsCount(int $hackatonId): int
    {
        return 20 + ($hackatonId % 21);
    }
}
