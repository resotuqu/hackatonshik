<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
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

        $p = 0;
        foreach (Team::query()->orderBy('id')->get() as $team) {
            foreach ($roles as $idx => $role) {
                TeamRole::query()->create([
                    'title' => $role->name.' в команде',
                    'description' => $faker->sentence(10),
                    'team_id' => $team->id,
                    'role_id' => $role->id,
                    'user_id' => $idx < 2 ? $participants[$p % $participants->count()]->id : null,
                ]);
                if ($idx < 2) {
                    $p++;
                }
            }
        }
    }
}
