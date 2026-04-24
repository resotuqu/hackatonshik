<?php

namespace Database\Factories;

use App\Models\HackatonCase;
use App\Models\HackatonCaseSubmission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonCaseSubmission>
 */
class HackatonCaseSubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_case_id' => HackatonCase::factory(),
            'team_id' => Team::factory(),
            'user_id' => null,
            'submitted_by_user_id' => User::factory(),
            'submitted_at' => now(),
        ];
    }
}
