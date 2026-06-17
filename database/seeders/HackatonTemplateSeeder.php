<?php

namespace Database\Seeders;

use App\Models\HackatonTemplate;
use Illuminate\Database\Seeder;

class HackatonTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'title' => 'GameJam',
                'slug' => 'gamejam',
                'description' => 'Короткий игровой хакатон: команды создают прототип игры за выходные.',
                'level' => 'beginner',
                'start_offset_days' => 14,
                'end_offset_days' => 16,
                'registration_deadline_offset_days' => 10,
                'default_documents' => [
                    [
                        'name' => 'Положение о проведении',
                        'description' => 'Правила и регламент GameJam.',
                        'filling_by_team_member' => false,
                    ],
                    [
                        'name' => 'Согласие на обработку персональных данных',
                        'description' => 'Заполняет каждый участник команды.',
                        'filling_by_team_member' => true,
                    ],
                ],
                'sort_order' => 1,
            ],
            [
                'title' => 'AI Hack',
                'slug' => 'ai-hack',
                'description' => 'Хакатон по машинному обучению и прикладному ИИ.',
                'level' => 'advanced',
                'start_offset_days' => 21,
                'end_offset_days' => 24,
                'registration_deadline_offset_days' => 14,
                'default_documents' => [
                    [
                        'name' => 'Регламент AI Hack',
                        'description' => 'Требования к решениям и использованию моделей.',
                        'filling_by_team_member' => false,
                    ],
                ],
                'sort_order' => 2,
            ],
            [
                'title' => 'Корпоративный',
                'slug' => 'corporate',
                'description' => 'Внутренний корпоративный хакатон с акцентом на бизнес-кейсы.',
                'level' => 'intermediate',
                'start_offset_days' => 30,
                'end_offset_days' => 32,
                'registration_deadline_offset_days' => 21,
                'default_documents' => [
                    [
                        'name' => 'NDA и согласие',
                        'description' => 'Обязательный документ для всех участников.',
                        'filling_by_team_member' => true,
                    ],
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($templates as $template) {
            HackatonTemplate::query()->updateOrCreate(
                ['slug' => $template['slug']],
                $template + ['is_active' => true],
            );
        }
    }
}
