<?php

namespace Database\Factories;

use App\Models\HackatonTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonTemplate>
 */
class HackatonTemplateFactory extends Factory
{
    protected $model = HackatonTemplate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'title' => $title,
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'level' => 'beginner',
            'start_offset_days' => 14,
            'end_offset_days' => 16,
            'registration_deadline_offset_days' => 10,
            'default_documents' => [],
            'default_case' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
