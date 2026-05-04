<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\HackatonImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonImage>
 */
class HackatonImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'path' => 'hackaton_gallery/'.fake()->uuid().'.png',
            'sort_order' => fake()->numberBetween(0, 10),
            'alt' => fake()->optional(0.5)->sentence(3),
        ];
    }
}
