<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\HackatonAnalyticsEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonAnalyticsEvent>
 */
class HackatonAnalyticsEventFactory extends Factory
{
    protected $model = HackatonAnalyticsEvent::class;

    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'user_id' => User::factory(),
            'event_name' => fake()->randomElement(['page_view', 'application_submitted']),
            'payload' => [],
        ];
    }
}
