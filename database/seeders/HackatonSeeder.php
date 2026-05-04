<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\HackatonLevel;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HackatonSeeder extends Seeder
{
    public function run(): void
    {
        $partners = User::query()->where('role', 'partner')->orderBy('id')->get();
        if ($partners->isEmpty()) {
            $this->command?->warn('Нет пользователей с ролью partner — пропуск HackatonSeeder.');

            return;
        }

        $p0 = $partners[0];
        $p1 = $partners->get(1, $p0);

        $now = Carbon::now()->startOfDay();

        $definitions = [
            [
                'user_id' => $p0->id,
                'title' => 'Внутренний черновик: лаборатория идей',
                'description' => 'Черновик хакатона для команды организаторов. Описание и регламент ещё уточняются.',
                'start_at' => $now->copy()->addDays(14),
                'end_at' => $now->copy()->addDays(15),
                'is_public' => false,
                'status' => HackatonStatus::DRAFT,
                'prize_fund' => null,
                'prize_places_count' => null,
                'level' => HackatonLevel::Beginner,
                'registration_deadline_at' => $now->copy()->addDays(7),
            ],
            [
                'user_id' => $p0->id,
                'title' => 'Спринт «Один день — один продукт»',
                'description' => 'Короткий формат: за сутки команды собирают MVP и презентуют жюри.',
                'start_at' => $now->copy()->subDays(45),
                'end_at' => $now->copy()->subDays(44),
                'is_public' => true,
                'status' => HackatonStatus::FINISHED,
                'prize_fund' => 150000,
                'prize_places_count' => 3,
                'level' => HackatonLevel::Intermediate,
                'registration_deadline_at' => $now->copy()->subDays(50),
            ],
            [
                'user_id' => $p1->id,
                'title' => 'Уикенд-код: цифровые сервисы для города',
                'description' => 'Три дня непрерывной разработки решений для умного города и гражданских инициатив.',
                'start_at' => $now->copy()->subDays(25),
                'end_at' => $now->copy()->subDays(22),
                'is_public' => true,
                'status' => HackatonStatus::FINISHED,
                'prize_fund' => 500000,
                'prize_places_count' => 5,
                'level' => HackatonLevel::Advanced,
                'registration_deadline_at' => $now->copy()->subDays(28),
            ],
            [
                'user_id' => $p0->id,
                'title' => 'Неделя инноваций в образовании',
                'description' => 'Семь дней на прототипы платформ обучения, трекеров прогресса и инструментов для преподавателей.',
                'start_at' => $now->copy()->subDays(3),
                'end_at' => $now->copy()->addDays(4),
                'is_public' => true,
                'status' => HackatonStatus::IN_PROGRESS,
                'prize_fund' => 750000,
                'prize_places_count' => 10,
                'level' => HackatonLevel::Intermediate,
                'registration_deadline_at' => $now->copy()->subDays(5),
            ],
            [
                'user_id' => $p1->id,
                'title' => 'Двухнедельный марафон FinTech',
                'description' => 'Четырнадцать дней на полноценный бэкенд, интеграции и демо-сценарии для финансовых сервисов.',
                'start_at' => $now->copy()->addDays(20),
                'end_at' => $now->copy()->addDays(34),
                'is_public' => true,
                'status' => HackatonStatus::REGISTRATION_OPEN,
                'prize_fund' => 1200000,
                'prize_places_count' => 5,
                'level' => HackatonLevel::Pro,
                'registration_deadline_at' => $now->copy()->addDays(15),
            ],
            [
                'user_id' => $p1->id,
                'title' => 'Месяц устойчивых ИТ-решений',
                'description' => 'Длинный формат: тридцать дней на исследование, разработку и пилот экологичных и социальных продуктов.',
                'start_at' => $now->copy()->addDays(45),
                'end_at' => $now->copy()->addDays(75),
                'is_public' => true,
                'status' => HackatonStatus::PUBLISHED,
                'prize_fund' => 2000000,
                'prize_places_count' => 7,
                'level' => HackatonLevel::Advanced,
                'registration_deadline_at' => $now->copy()->addDays(40),
            ],
            [
                'user_id' => $p0->id,
                'title' => 'Судейский этап: HealthTech 2025',
                'description' => 'Событие завершило основную фазу; команды ожидают финальных оценок экспертного жюри.',
                'start_at' => $now->copy()->subDays(18),
                'end_at' => $now->copy()->subDays(4),
                'is_public' => true,
                'status' => HackatonStatus::JUDGING,
                'prize_fund' => 900000,
                'prize_places_count' => 3,
                'level' => HackatonLevel::Pro,
                'registration_deadline_at' => $now->copy()->subDays(22),
            ],
        ];

        foreach ($definitions as $data) {
            $data['image_url'] = 'hackaton_photos/default.png';
            Hackaton::query()->create($data);
        }
    }
}
