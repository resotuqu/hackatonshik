<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamApplication>
 */
class TeamApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_role_id' => TeamRole::factory(),
            'message' => fake()->optional(0.6)->sentence(),
            'status' => ApplicationStatus::PENDING,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ];
    }
}
