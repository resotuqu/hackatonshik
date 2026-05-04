<?php

namespace Database\Factories;

use App\Models\Hackaton;
use App\Models\HackatonDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonDocument>
 */
class HackatonDocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_id' => Hackaton::factory(),
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(8),
            'file_url' => 'hackaton_documents/'.fake()->uuid().'.pdf',
            'filling_by_team_member' => fake()->boolean(40),
        ];
    }
}
