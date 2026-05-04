<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $applicants = User::query()->where('role', 'user')->orderBy('id')->get();
        if ($applicants->isEmpty()) {
            return;
        }

        $openRoles = TeamRole::query()
            ->whereNull('user_id')
            ->orderBy('id')
            ->take(20)
            ->get();

        $i = 0;
        foreach ($openRoles as $role) {
            $user = $applicants[$i % $applicants->count()];
            $i++;

            TeamApplication::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'team_role_id' => $role->id,
                ],
                [
                    'message' => 'Хочу присоединиться к команде в указанной роли.',
                    'status' => ApplicationStatus::ACCEPTED,
                    'reviewed_at' => now()->subDay(),
                    'reviewed_by' => $role->team->user_id,
                ],
            );
        }
    }
}
