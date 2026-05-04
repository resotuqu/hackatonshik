<?php

namespace Database\Factories;

use App\Enums\HackatonLevel;
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
        $registrationDeadlineAt = fake()->dateTimeBetween('+1 days', $startAt);

        return [
            'user_id' => User::factory()->partner(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'image_url' => 'hackaton_photos/default.png',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'is_public' => true,
            'status' => HackatonStatus::REGISTRATION_OPEN,
            'prize_fund' => fake()->randomElement([null, 50000, 100000, 250000, 500000, 1000000]),
            'prize_places_count' => fake()->randomElement([null, 1, 3, 5, 10]),
            'level' => fake()->randomElement(HackatonLevel::cases()),
            'registration_deadline_at' => $registrationDeadlineAt,
        ];
    }
}
