<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HackatonCaseAnswer;
use App\Models\HackatonCaseField;
use App\Models\HackatonCaseSubmission;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class HackatonCaseAnswerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('ru_RU');

        foreach (HackatonCaseSubmission::query()->with('case.fields')->orderBy('id')->get() as $submission) {
            $fields = $submission->case?->fields ?? collect();
            foreach ($fields as $field) {
                $text = match ($field->type) {
                    HackatonCaseField::TYPE_URL => 'https://github.com/demo/'.fake()->slug(2),
                    default => $faker->realText(80),
                };

                HackatonCaseAnswer::query()->firstOrCreate(
                    [
                        'hackaton_case_submission_id' => $submission->id,
                        'hackaton_case_field_id' => $field->id,
                    ],
                    [
                        'value_text' => $text,
                        'value_json' => null,
                        'file_path' => null,
                    ],
                );
            }
        }
    }
}
