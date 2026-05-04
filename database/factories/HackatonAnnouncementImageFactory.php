<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\HackatonAnnouncement;
use App\Models\HackatonAnnouncementImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonAnnouncementImage>
 */
class HackatonAnnouncementImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_announcement_id' => HackatonAnnouncement::factory(),
            'path' => 'announcement_images/'.fake()->uuid().'.jpg',
            'sort_order' => fake()->numberBetween(0, 5),
            'alt' => fake()->optional(0.6)->sentence(2),
        ];
    }
}
