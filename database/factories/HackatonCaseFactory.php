<?php

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonCase>
 */
class HackatonCaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => true,
        ];
    }
}
