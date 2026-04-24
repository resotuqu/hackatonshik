<?php

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hackaton>
 */
class HackatonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->partner(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'image_url' => 'hackaton_photos/default.png',
            'start_at' => fake()->date(),
            'end_at' => fake()->date(),
            'is_public' => true,
        ];
    }
}
