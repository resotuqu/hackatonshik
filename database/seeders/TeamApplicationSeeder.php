<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
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
            ->get();

        $i = 0;
        foreach ($openRoles as $role) {
            $user = $applicants[$i % $applicants->count()];
            $i++;
            $status = $role->team->hackaton->status;
            $isReviewStage = in_array($status, [
                HackatonStatus::REGISTRATION_CLOSED,
                HackatonStatus::WAITING_START,
                HackatonStatus::CASES_ANNOUNCED,
                HackatonStatus::IN_PROGRESS,
                HackatonStatus::JUDGING,
                HackatonStatus::FINISHED,
                HackatonStatus::ARCHIVED,
            ], true);

            TeamApplication::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'team_role_id' => $role->id,
                ],
                [
                    'message' => 'Хочу присоединиться к команде в указанной роли.',
                    'status' => $isReviewStage ? ApplicationStatus::ACCEPTED : ApplicationStatus::PENDING,
                    'reviewed_at' => $isReviewStage ? now()->subDay() : null,
                    'reviewed_by' => $isReviewStage ? $role->team->user_id : null,
                ],
            );
        }
    }
}
