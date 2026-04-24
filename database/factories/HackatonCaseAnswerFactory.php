<?php

namespace Database\Factories;

use App\Models\HackatonCaseAnswer;
use App\Models\HackatonCaseField;
use App\Models\HackatonCaseSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HackatonCaseAnswer>
 */
class HackatonCaseAnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hackaton_case_submission_id' => HackatonCaseSubmission::factory(),
            'hackaton_case_field_id' => HackatonCaseField::factory(),
            'value_text' => fake()->sentence(),
            'value_json' => null,
            'file_path' => null,
        ];
    }
}
