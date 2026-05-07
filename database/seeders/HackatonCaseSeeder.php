<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseField;
use App\Models\HackatonCaseImage;
use Illuminate\Database\Seeder;

class HackatonCaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Hackaton::query()->orderBy('id')->get() as $hackaton) {
            $cases = [
                [
                    'title' => 'Кейс: разработка системы мониторинга',
                    'description' => "## Задача\nРазработать прототип системы мониторинга городских ресурсов. \n\n### Ожидаемый результат\n- Веб-панель управления\n- API для сенсоров\n- Мобильное приложение для граждан",
                    'resources_json' => [
                        ['label' => 'Чат в Telegram для обсуждения', 'url' => 'https://t.me/hackaton_monitoring_chat'],
                        ['label' => 'Техническое задание (PDF)', 'url' => 'https://example.com/tz_monitoring.pdf'],
                    ],
                    'fields' => [
                        ['label' => 'Название проекта', 'key' => 'solution_title', 'type' => HackatonCaseField::TYPE_TEXT],
                        ['label' => 'Ссылка на GitHub', 'key' => 'repo_url', 'type' => HackatonCaseField::TYPE_URL],
                        ['label' => 'Ссылка на Figma', 'key' => 'figma_url', 'type' => HackatonCaseField::TYPE_URL],
                        ['label' => 'Техническое описание', 'key' => 'details', 'type' => HackatonCaseField::TYPE_TEXTAREA],
                    ],
                ],
                [
                    'title' => 'Кейс: ИИ-помощник для студентов',
                    'description' => 'Создайте чат-бота или платформу, которая помогает студентам ориентироваться в учебном процессе с помощью LLM.',
                    'resources_json' => [
                        ['label' => 'Доступ к API (Discord)', 'url' => 'https://discord.gg/ai_student_helper'],
                    ],
                    'fields' => [
                        ['label' => 'Ссылка на презентацию', 'key' => 'presentation_url', 'type' => HackatonCaseField::TYPE_URL],
                        ['label' => 'Комментарий к решению', 'key' => 'demo_notes', 'type' => HackatonCaseField::TYPE_TEXTAREA],
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
                    'resources_json' => $caseData['resources_json'] ?? null,
                    'sort_order' => $order,
                    'is_published' => true,
                ]);

                // Create dummy images
                for ($i = 1; $i <= 3; $i++) {
                    HackatonCaseImage::query()->create([
                        'hackaton_case_id' => $case->id,
                        'path' => 'case_photos/default.png',
                        'alt' => "Изображение {$i} для кейса {$case->title}",
                        'sort_order' => $i,
                    ]);
                }

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
