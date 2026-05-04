<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use Illuminate\Database\Seeder;

class HackatonCaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Hackaton::query()->orderBy('id')->get() as $hackaton) {
            $cases = [
                [
                    'title' => 'Кейс: описание решения и ссылка на репозиторий',
                    'description' => 'Опишите архитектуру, стек технологий и приложите ссылку на исходный код.',
                    'fields' => [
                        ['label' => 'Краткое название решения', 'key' => 'solution_title', 'type' => HackatonCaseField::TYPE_TEXT],
                        ['label' => 'Ссылка на репозиторий', 'key' => 'repo_url', 'type' => HackatonCaseField::TYPE_URL],
                        ['label' => 'Подробное описание', 'key' => 'details', 'type' => HackatonCaseField::TYPE_TEXTAREA],
                    ],
                ],
                [
                    'title' => 'Кейс: демо и материалы',
                    'description' => 'Ссылка на развёрнутое демо и комментарии для жюри.',
                    'fields' => [
                        ['label' => 'URL демо', 'key' => 'demo_url', 'type' => HackatonCaseField::TYPE_URL],
                        ['label' => 'Комментарий к демо', 'key' => 'demo_notes', 'type' => HackatonCaseField::TYPE_TEXTAREA],
                    ],
                ],
            ];

            foreach ($cases as $order => $caseData) {
                $fields = $caseData['fields'];
                unset($caseData['fields']);

                $case = HackatonCase::query()->create([
                    'hackaton_id' => $hackaton->id,
                    'title' => $caseData['title'],
                    'description' => $caseData['description'],
                    'sort_order' => $order,
                    'is_published' => true,
                ]);

                foreach ($fields as $i => $field) {
                    HackatonCaseField::query()->create([
                        'hackaton_case_id' => $case->id,
                        'label' => $field['label'],
                        'key' => $field['key'].'_'.$case->id,
                        'type' => $field['type'],
                        'is_required' => $i === 0,
                        'sort_order' => $i,
                        'options_json' => null,
                    ]);
                }
            }
        }
    }
}
