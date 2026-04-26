<?php

namespace Database\Factories;

use App\Enums\HackatonStatus;
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
        $startAt = fake()->dateTimeBetween('+3 days', '+20 days');
        $endAt = fake()->dateTimeBetween($startAt, '+30 days');

        return [
            'user_id' => User::factory()->partner(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'image_url' => 'hackaton_photos/default.png',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'is_public' => true,
            'status' => HackatonStatus::REGISTRATION_OPEN,
        ];
    }
}
