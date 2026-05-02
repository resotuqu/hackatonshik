<?php

use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'О нас'])] class extends Component
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

};
?>

<div class="mx-auto mt-8 w-full max-w-6xl space-y-8 sm:mt-12 sm:space-y-12">
    <section class="rounded-3xl border border-primary/15 bg-base-100 p-6 shadow-md shadow-primary/5 sm:p-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-3">
                <h1 class="font-display text-3xl font-bold sm:text-4xl">О Хакатонщике</h1>
                <p class="max-w-2xl text-base-content/75">
                    Мы делаем хакатоны доступнее: помогаем участникам находить команды и роли,
                    а организаторам — собирать сильные составы без хаоса в чатах и таблицах.
                </p>
            </div>
            <img src="/logo.svg" class="mx-auto h-auto w-48 sm:mx-0 sm:w-64" alt="Логотип Хакатонщика">
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-marycard title="Проведено хакатонов" class="card card-border border-base-300 bg-base-100 shadow-sm">
            <p class="text-3xl font-semibold">{{ $impact['hackatons'] ?? 0 }}</p>
        </x-marycard>
        <x-marycard title="Участников" class="card card-border border-base-300 bg-base-100 shadow-sm">
            <p class="text-3xl font-semibold">{{ $impact['participants'] ?? 0 }}</p>
        </x-marycard>
        <x-marycard title="Команд" class="card card-border border-base-300 bg-base-100 shadow-sm">
            <p class="text-3xl font-semibold">{{ $impact['teams'] ?? 0 }}</p>
        </x-marycard>
        <x-marycard title="Пользователей" class="card card-border border-base-300 bg-base-100 shadow-sm">
            <p class="text-3xl font-semibold">{{ $impact['users'] ?? 0 }}</p>
        </x-marycard>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-marycard title="Наша миссия" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
            Создать единое пространство для хакатонов России, где участники, команды и организаторы
            быстро находят друг друга и фокусируются на результате.
        </x-marycard>

        <x-marycard title="Для кого мы" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
            Для участников, которые ищут роль по навыкам, для команд, которым не хватает людей,
            и для партнеров, которым нужен прозрачный отбор.
        </x-marycard>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">Наши ценности</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-marycard title="Прозрачность" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
                Понятные статусы заявок и единые правила коммуникации.
            </x-marycard>
            <x-marycard title="Удобство" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
                Простые формы и быстрые сценарии без лишних действий.
            </x-marycard>
            <x-marycard title="Сообщество" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
                Поддерживаем развитие хакатон-экосистемы через сотрудничество и обмен опытом.
            </x-marycard>
        </div>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">Команда и организаторы</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach ($organizers as $organizer)
                <x-marycard :title="$organizer['name']" class="card card-border border-base-300 bg-base-100 shadow-sm">
                    <p class="text-sm font-medium text-primary">{{ $organizer['role'] }}</p>
                    <p class="mt-2 text-sm text-base-content/80">{{ $organizer['about'] }}</p>
                </x-marycard>
            @endforeach
        </div>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">История проекта</h2>
        <x-marycard class="card card-border border-base-300 bg-base-100 shadow-sm">
            <ul class="timeline timeline-vertical">
                @foreach ($history as $item)
                    <li>
                        @if (! $loop->first)
                            <hr />
                        @endif
                        <div class="timeline-start text-sm text-base-content/70">{{ $item['period'] }}</div>
                        <div class="timeline-middle text-primary">●</div>
                        <div class="timeline-end timeline-box">
                            <p class="font-semibold">{{ $item['title'] }}</p>
                            <p class="text-sm text-base-content/70">{{ $item['description'] }}</p>
                        </div>
                        @if (! $loop->last)
                            <hr />
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-marycard>
    </section>

    <section class="rounded-2xl border border-primary/15 bg-base-100 p-6 shadow-md shadow-primary/5">
        <h2 class="font-display text-2xl font-bold">Присоединяйтесь</h2>
        <p class="mt-2 text-base-content/75">
            Если вы участник, соберите профиль и найдите команду. Если вы организатор,
            публикуйте хакатон и управляйте заявками в одном месте.
        </p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="/teams" class="btn btn-primary">Смотреть команды</a>
            <a href="/hackatons" class="btn btn-outline">Смотреть хакатоны</a>
        </div>
    </section>
</div>
