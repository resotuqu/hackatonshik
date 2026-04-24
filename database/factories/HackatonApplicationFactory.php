<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonApplication>
 */
class HackatonApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'hackaton_id' => Hackaton::factory(),
            'message' => fake()->sentence(),
            'status' => ApplicationStatus::PENDING->value,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ];
    }
}
