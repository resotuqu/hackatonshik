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
        $status = fake()->randomElement([
            HackatonStatus::REGISTRATION_OPEN,
            HackatonStatus::REGISTRATION_CLOSED,
            HackatonStatus::WAITING_START,
            HackatonStatus::CASES_ANNOUNCED,
            HackatonStatus::IN_PROGRESS,
        ]);

        $timeline = match ($status) {
            HackatonStatus::REGISTRATION_OPEN => [
                'start' => fake()->dateTimeBetween('+6 days', '+20 days'),
                'end' => fake()->dateTimeBetween('+21 days', '+30 days'),
                'deadline' => fake()->dateTimeBetween('+1 days', '+5 days'),
            ],
            HackatonStatus::REGISTRATION_CLOSED => [
                'start' => fake()->dateTimeBetween('+6 days', '+12 days'),
                'end' => fake()->dateTimeBetween('+13 days', '+18 days'),
                'deadline' => fake()->dateTimeBetween('-4 days', '-1 days'),
            ],
            HackatonStatus::WAITING_START, HackatonStatus::CASES_ANNOUNCED => [
                'start' => fake()->dateTimeBetween('+1 days', '+2 days'),
                'end' => fake()->dateTimeBetween('+3 days', '+6 days'),
                'deadline' => fake()->dateTimeBetween('-6 days', '-2 days'),
            ],
            default => [
                'start' => fake()->dateTimeBetween('-2 days', '-1 days'),
                'end' => fake()->dateTimeBetween('+1 days', '+4 days'),
                'deadline' => fake()->dateTimeBetween('-10 days', '-5 days'),
            ],
        };

        return [
            'user_id' => User::factory()->partner(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'image_url' => 'hackaton_photos/default.png',
            'start_at' => $timeline['start'],
            'end_at' => $timeline['end'],
            'is_public' => true,
            'status' => $status,
            'prize_fund' => fake()->randomElement([null, 50000, 100000, 250000, 500000, 1000000]),
            'prize_places_count' => fake()->randomElement([null, 1, 3, 5, 10]),
            'level' => fake()->randomElement(HackatonLevel::cases()),
            'registration_deadline_at' => $timeline['deadline'],
        ];
    }
}
