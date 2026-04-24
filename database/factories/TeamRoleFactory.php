<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Team;
use App\Models\TeamRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamRole>
 */
class TeamRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->sentence(8),
            'team_id' => Team::factory(),
            'role_id' => Role::factory(),
            'user_id' => null,
        ];
    }
}
