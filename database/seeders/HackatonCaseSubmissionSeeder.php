<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use Illuminate\Database\Seeder;

class HackatonCaseSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (HackatonCase::query()->orderBy('id')->get() as $case) {
            $teams = Team::query()->where('hackaton_id', $case->hackaton_id)->orderBy('id')->get();
            foreach ($teams as $team) {
                HackatonCaseSubmission::query()->firstOrCreate(
                    [
                        'hackaton_case_id' => $case->id,
                        'team_id' => $team->id,
                    ],
                    [
                        'user_id' => null,
                        'submitted_by_user_id' => $team->user_id,
                        'submitted_at' => now()->subHours(random_int(1, 48)),
                    ],
                );
            }
        }
    }
}
