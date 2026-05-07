<?php

namespace App\Livewire\Pages\About;

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app', ['title' => 'О нас'])]
class Index extends Component
{
    public array $impact = [];

    public array $organizers = [];

    public array $history = [];

    public function mount(): void
    {
        $this->impact = Cache::remember('about-impact-metrics', now()->addMinutes(15), function (): array {
            $hackatons = Hackaton::query()->get();

            return [
                'hackatons' => $hackatons->count(),
                'participants' => $hackatons->sum(fn (Hackaton $hackaton) => $hackaton->participantsCount()),
                'teams' => Team::query()->count(),
                'users' => User::query()->count(),
            ];
        });

        $this->organizers = [
            ['name' => 'Владимир Сергеев', 'role' => 'Основатель и организатор', 'about' => 'Развивает платформу и коммуникацию с организаторами хакатонов.'],
            ['name' => 'Команда Хакатонщика', 'role' => 'Продукт и поддержка', 'about' => 'Помогает участникам и партнерам запускать хакатоны без лишней рутины.'],
            ['name' => 'Эксперты сообщества', 'role' => 'Судьи и менторы', 'about' => 'Делятся экспертизой и поддерживают рост участников.'],
        ];

        $this->history = [
            ['period' => '2025', 'title' => 'Идея и прототип', 'description' => 'Сформировали сценарии для участников и организаторов, собрали первые интервью.'],
            ['period' => '2026 Q1', 'title' => 'Запуск MVP', 'description' => 'Запустили личные кабинеты, команды, заявки и страницу хакатонов.'],
            ['period' => '2026 Q2', 'title' => 'Рост экосистемы', 'description' => 'Добавили кейсы, уведомления, анонсы и сертификаты на одной платформе.'],
            ['period' => 'Далее', 'title' => 'Масштабирование', 'description' => 'Расширяем инструменты модерации и аналитики для партнеров и администраторов.'],
        ];
    }

    public function render()
    {
        return view('pages.about.index');
    }
}
