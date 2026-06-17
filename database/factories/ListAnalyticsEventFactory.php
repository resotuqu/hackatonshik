<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ListAnalyticsEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ListAnalyticsEvent>
 */
class ListAnalyticsEventFactory extends Factory
{
    protected $model = ListAnalyticsEvent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'list_key' => fake()->randomElement(['hackatons', 'teams']),
            'event_name' => fake()->randomElement(['list_view', 'filter_apply', 'card_open']),
            'payload' => ['source' => fake()->word()],
        ];
    }
}
