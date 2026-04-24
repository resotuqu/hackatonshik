<?php

namespace Database\Factories;

use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonCaseField>
 */
class HackatonCaseFieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_case_id' => HackatonCase::factory(),
            'label' => fake()->sentence(2),
            'key' => fake()->unique()->slug(),
            'type' => fake()->randomElement(HackatonCaseField::allowedTypes()),
            'is_required' => false,
            'sort_order' => fake()->numberBetween(0, 20),
            'options_json' => null,
        ];
    }
}
