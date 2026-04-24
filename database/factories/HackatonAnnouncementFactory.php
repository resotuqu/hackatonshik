<?php

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\HackatonAnnouncement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonAnnouncement>
 */
class HackatonAnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'created_by' => User::factory()->partner(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'published_at' => now(),
        ];
    }
}
