<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\HackatonStatus;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class HackatonCaseScoreSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('ru_RU');
        $judge = User::query()->where('role', 'judge')->orderBy('id')->first();
        if (! $judge) {
            return;
        }

        $submissionIds = HackatonCaseSubmission::query()
            ->whereHas('case.hackaton', function ($q): void {
                $q->whereIn('status', [
                    HackatonStatus::JUDGING,
                    HackatonStatus::FINISHED,
                    HackatonStatus::IN_PROGRESS,
                ]);
            })
            ->orderBy('id')
            ->pluck('id');

        foreach ($submissionIds->take(25) as $submissionId) {
            HackatonCaseScore::query()->firstOrCreate(
                [
                    'hackaton_case_submission_id' => $submissionId,
                    'reviewed_by' => $judge->id,
                ],
                [
                    'score' => random_int(55, 98),
                    'max_score' => 100,
                    'general_comment' => $faker->optional(0.8)->realText(200),
                    'is_final' => true,
                    'reviewed_at' => now()->subHours(random_int(1, 72)),
                ],
            );
        }
    }
}
