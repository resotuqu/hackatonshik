<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonCaseScore>
 */
class HackatonCaseScoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_case_submission_id' => HackatonCaseSubmission::factory(),
            'reviewed_by' => User::factory()->judge(),
            'score' => fake()->numberBetween(40, 100),
            'max_score' => 100,
            'general_comment' => fake()->optional(0.7)->paragraph(),
            'is_final' => true,
            'reviewed_at' => now(),
        ];
    }
}
