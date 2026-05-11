<?php

namespace Database\Factories;

use App\Enums\JudgeDomain;
use App\Models\Hackaton;
use App\Models\HackatonCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonCase>
 */
class HackatonCaseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => true,
            'rubric_json' => [
                [
                    'id' => 'solution',
                    'label' => 'Качество решения',
                    'max' => 10,
                    'domain' => JudgeDomain::DEV->value,
                ],
                [
                    'id' => 'ux',
                    'label' => 'UX/UI',
                    'max' => 10,
                    'domain' => JudgeDomain::DESIGN->value,
                ],
                [
                    'id' => 'value',
                    'label' => 'Бизнес-ценность',
                    'max' => 10,
                    'domain' => JudgeDomain::BUSINESS->value,
                ],
            ],
        ];
    }
}
