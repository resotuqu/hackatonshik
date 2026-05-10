<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonStatus;
use App\Models\HackatonApplication;
use App\Models\Team;
use Illuminate\Database\Seeder;

class HackatonApplicationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Team::query()->orderBy('id')->get() as $team) {
            $isReviewStage = in_array($team->hackaton->status, [
                HackatonStatus::REGISTRATION_CLOSED,
                HackatonStatus::WAITING_START,
                HackatonStatus::CASES_ANNOUNCED,
                HackatonStatus::IN_PROGRESS,
                HackatonStatus::JUDGING,
                HackatonStatus::FINISHED,
                HackatonStatus::ARCHIVED,
            ], true);

            HackatonApplication::query()->firstOrCreate(
                [
                    'team_id' => $team->id,
                    'hackaton_id' => $team->hackaton_id,
                ],
                [
                    'message' => 'Просим принять нашу команду на хакатон. Готовы к любым форматам проверки.',
                    'status' => $isReviewStage ? ApplicationStatus::ACCEPTED : ApplicationStatus::PENDING,
                    'reviewed_at' => $isReviewStage ? now()->subDays(2) : null,
                    'reviewed_by' => $isReviewStage ? $team->hackaton->user_id : null,
                ],
            );
        }
    }
}
