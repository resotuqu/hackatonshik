<?php

use App\Enums\ApplicationStatus;
use App\Models\HackatonApplication;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Главная'])] class extends Component {
    public int $teamsCount = 0;

    public int $certificatesCount = 0;

    public int $pendingTeamApplicationsCount = 0;

    public int $hackatonsCount = 0;

    public int $pendingHackatonApplicationsCount = 0;

    public int $judgeHackatonsCount = 0;

    /** @var list<array{id: int, title: string, start_at: string|null}> */
    public array $judgeHackatonsPreview = [];

    public int $usersCount = 0;

    public function mount(): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        if (! $user instanceof User) {
            return;
        }

        if ($user->isParticipant()) {
            $this->teamsCount = $user->teams()->count();
            $this->certificatesCount = $user->certificates()->count();
            $this->pendingTeamApplicationsCount = $user->teamApplications()
                ->where('status', ApplicationStatus::PENDING)
                ->count();

            return;
        }

        if ($user->isOrganizer()) {
            $this->hackatonsCount = $user->hackatons()->count();
            $this->pendingHackatonApplicationsCount = HackatonApplication::query()
                ->where('status', ApplicationStatus::PENDING)
                ->whereHas('hackaton', fn ($query) => $query->where('user_id', $user->id))
                ->count();

            return;
        }

        if ($user->isJudge()) {
            $this->judgeHackatonsCount = $user->judgedHackatons()->count();
            $this->judgeHackatonsPreview = $user->judgedHackatons()
                ->orderBy('start_at')
                ->limit(5)
                ->get()
                ->map(fn ($hackaton) => [
                    'id' => $hackaton->id,
                    'title' => $hackaton->title,
                    'start_at' => $hackaton->start_at?->translatedFormat('d.m.Y H:i'),
                ])
                ->all();

            return;
        }

        if ($user->isAdmin()) {
            $this->usersCount = User::query()->count();
        }
    }
};

?>

@guest
<div class="mx-auto w-full max-w-7xl space-y-12">
    <section id="start" class="hero min-h-[70vh] rounded-3xl bg-base-100 px-4 py-8 shadow-sm sm:min-h-[75vh]">
        <div class="hero-content text-center">
            <div class="w-full max-w-xl space-y-5">
                <figure class="mx-auto max-w-52 rounded-2xl bg-base-200 p-3">
                    <img src="/logo.svg" alt="Логотип Хакатонщика" class="h-auto w-full" />
                </figure>
                <h1 class="text-4xl font-bold sm:text-5xl">Путь участника и организатора в одном месте</h1>
                <p class="text-base-content/80">
                    Создавайте команды, подавайте заявки, решайте кейсы и получайте сертификаты без переписки в чатах и почте.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="/register" class="btn btn-primary">Зарегистрироваться и начать</a>
                    <a href="/login" class="btn btn-outline">У меня уже есть аккаунт</a>
                </div>
            </div>
        </div>
    </section>

    <section id="purpose" class="space-y-6">
        <h2 class="text-center text-3xl font-bold">Как это работает</h2>
        <p class="mx-auto max-w-3xl text-center text-base-content/80">
            Четкая последовательность шагов помогает быстро понять, что делать дальше на каждом этапе.
        </p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-marycard title="1. Создайте команду" class="card card-border bg-base-100 shadow-sm">
                Соберите участников и распределите роли в пару кликов.
            </x-marycard>

            <x-marycard title="2. Подайте заявку" class="card card-border bg-base-100 shadow-sm">
                Выберите хакатон и отправьте заявку от команды.
            </x-marycard>

            <x-marycard title="3. Решайте кейсы" class="card card-border bg-base-100 shadow-sm">
                Заполняйте поля кейса и загружайте материалы в одном месте.
            </x-marycard>

            <x-marycard title="4. Получайте результаты" class="card card-border bg-base-100 shadow-sm">
                Следите за статусами, анонсами и сертификатами прямо в профиле.
            </x-marycard>
        </div>
    </section>

    <section class="space-y-6">
        <h2 class="text-center text-3xl font-bold">Почему это удобно</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-marycard title="Фильтрация под вас" class="card card-border bg-base-100 shadow-sm">
                Найдите роль под свои возможности.
                <x-slot:figure>
                    <img src="/images/pros-1.png" alt="Фильтрация ролей" />
                </x-slot:figure>
            </x-marycard>

            <x-marycard title="Удобство использования" class="card card-border bg-base-100 shadow-sm">
                Больше не надо отправлять документы на почту или в чат сотрудника.
                <x-slot:figure>
                    <img src="/images/pros-3.png" alt="Удобство использования" />
                </x-slot:figure>
            </x-marycard>
        </div>
    </section>
</div>
@endguest

@auth
<div class="mx-auto w-full max-w-7xl space-y-10" data-test="home-dashboard">
    <header class="space-y-2">
        <h1 class="text-3xl font-bold sm:text-4xl">Краткая сводка</h1>
        <p class="text-base-content/80">
            Здравствуйте, {{ auth()->user()->fio }}.
        </p>
    </header>

    @if (auth()->user()->isParticipant())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-marycard title="Мои команды" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $teamsCount }}</p>
                <x-slot:menu>
                    <a href="/profile/teams" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Сертификаты" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $certificatesCount }}</p>
                <x-slot:menu>
                    <a href="/profile/certificates" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Заявки в команду на рассмотрении" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $pendingTeamApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">Заявки, которые вы подали на участие в ролях команд.</p>
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="/hackatons" class="btn btn-primary">Хакатоны</a>
            <a href="/teams/create" class="btn btn-outline">Создать команду</a>
            <a href="/profile/teams" class="btn btn-outline">Мои команды</a>
        </div>
    @elseif (auth()->user()->isOrganizer())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-marycard title="Мои хакатоны" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $hackatonsCount }}</p>
                <x-slot:menu>
                    <a href="/profile/hackatons" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Заявки команд на рассмотрении" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $pendingHackatonApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По всем вашим хакатонам.</p>
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="/profile/hackatons" class="btn btn-primary">Мои хакатоны</a>
            <a href="/hackatons/create" class="btn btn-outline">Создать хакатон</a>
            <a href="/hackatons" class="btn btn-outline">Каталог хакатонов</a>
        </div>
    @elseif (auth()->user()->isJudge())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-marycard title="Назначенные хакатоны" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $judgeHackatonsCount }}</p>
                <x-slot:menu>
                    <a href="/hackatons" class="btn btn-ghost btn-sm">Каталог</a>
                </x-slot:menu>
            </x-marycard>
        </div>
        @if (count($judgeHackatonsPreview) > 0)
            <x-marycard title="Ближайшие по дате начала" class="card card-border bg-base-100 shadow-sm w-full max-w-2xl">
                <ul class="space-y-2">
                    @foreach ($judgeHackatonsPreview as $row)
                        <li class="flex flex-wrap items-baseline justify-between gap-2 border-b border-base-200 pb-2 last:border-0">
                            <a href="{{ route('hackatons.show', $row['id']) }}" class="link link-primary font-medium">{{ $row['title'] }}</a>
                            @if ($row['start_at'])
                                <span class="text-sm text-base-content/70">{{ $row['start_at'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-marycard>
        @endif
        <div class="flex flex-wrap gap-3">
            <a href="/hackatons" class="btn btn-primary">Все хакатоны</a>
            <a href="/profile" class="btn btn-outline">Профиль</a>
        </div>
    @elseif (auth()->user()->isAdmin())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-marycard title="Пользователей в системе" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $usersCount }}</p>
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="/admin" class="btn btn-primary">Админ-панель</a>
            <a href="/hackatons" class="btn btn-outline">Хакатоны</a>
            <a href="/profile" class="btn btn-outline">Профиль</a>
        </div>
    @else
        <p class="text-base-content/80">Выберите раздел в меню слева или перейдите к хакатонам.</p>
        <div class="flex flex-wrap gap-3">
            <a href="/hackatons" class="btn btn-primary">Хакатоны</a>
            <a href="/profile" class="btn btn-outline">Профиль</a>
        </div>
    @endif
</div>
@endauth
