<?php

use App\Models\User;
use App\ViewModels\HomeDashboardData;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Главная'])] class extends Component {
    public int $teamsCount = 0;

    public int $certificatesCount = 0;

    public int $pendingTeamApplicationsCount = 0;

    public int $pendingHackatonApplicationsCount = 0;

    /** @var list<array{id: int, hackaton_id: int, title: string, team_title: string, status_label: string}> */
    public array $hackatonApplicationsPreview = [];

    /** @var list<array{id: int, title: string, start_at: string|null}> */
    public array $participantHackatonsPreview = [];

    public string $participantNextStepTitle = '';

    public string $participantNextStepHint = '';

    public ?string $participantNextStepHref = null;

    public ?string $participantNextStepLabel = null;

    public int $hackatonsCount = 0;

    public ?int $organizerFirstPendingHackatonId = null;

    public int $judgeHackatonsCount = 0;

    /** @var list<array{id: int, title: string, start_at: string|null}> */
    public array $judgeHackatonsPreview = [];

    public int $usersCount = 0;

    public int $adminHackatonsCount = 0;

    public int $adminPartnersCount = 0;

    public int $adminPendingApplicationsCount = 0;

    public int $unreadNotificationsCount = 0;

    public bool $showPhoneVerificationBanner = false;

    public function mount(): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        if (! $user instanceof User) {
            return;
        }

        foreach (HomeDashboardData::fromUser($user)->toLivewireArray() as $key => $value) {
            $this->{$key} = $value;
        }
    }
};

?>

@guest
<div class="mx-auto w-full max-w-7xl space-y-12">
    <section
        id="start"
        class="relative overflow-hidden rounded-3xl border border-primary/20 bg-base-100 px-4 py-12 shadow-lg shadow-primary/5 sm:min-h-[75vh] sm:py-16"
    >
        <div
            class="pointer-events-none absolute inset-0 opacity-90"
            aria-hidden="true"
            style="background: radial-gradient(1200px 600px at 10% -10%, oklch(56% 0.21 272 / 0.28), transparent 55%), radial-gradient(900px 500px at 90% 20%, oklch(82% 0.19 118 / 0.18), transparent 50%), radial-gradient(600px 400px at 50% 100%, oklch(22% 0.06 264 / 0.35), transparent 45%);"
        ></div>
        <div class="hero relative min-h-[65vh] sm:min-h-[70vh]">
            <div class="hero-content max-w-3xl text-center">
                <div class="w-full space-y-6">
                    <figure
                        class="mx-auto max-w-56 rounded-2xl border border-base-200 bg-base-100/80 p-4 shadow-md backdrop-blur-sm sm:max-w-md"
                    >
                        <img src="/logo.svg" alt="Логотип Хакатонщика" class="h-auto w-full" />
                    </figure>
                    <h1 class="font-display text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
                        Путь участника и организатора в одном месте
                    </h1>
                    <p class="mx-auto max-w-xl text-lg text-base-content/80">
                        Создавайте команды, подавайте заявки, решайте кейсы и получайте сертификаты без переписки в чатах и почте.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <a href="/register" class="btn btn-primary shadow-md shadow-primary/25">Зарегистрироваться и начать</a>
                        <a href="/login" class="btn btn-outline border-primary/35">У меня уже есть аккаунт</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="purpose" class="space-y-6">
        <h2 class="text-center font-display text-3xl font-bold">Как это работает</h2>
        <p class="mx-auto max-w-3xl text-center text-base-content/80">
            Четкая последовательность шагов помогает быстро понять, что делать дальше на каждом этапе.
        </p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-marycard title="1. Создайте команду" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                Соберите участников и распределите роли в пару кликов.
            </x-marycard>

            <x-marycard title="2. Подайте заявку" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                Выберите хакатон и отправьте заявку от команды.
            </x-marycard>

            <x-marycard title="3. Решайте кейсы" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                Заполняйте поля кейса и загружайте материалы в одном месте.
            </x-marycard>

            <x-marycard title="4. Получайте результаты" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                Следите за статусами, анонсами и сертификатами прямо в профиле.
            </x-marycard>
        </div>
    </section>

    <section class="space-y-6">
        <h2 class="text-center font-display text-3xl font-bold">Почему это удобно</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-marycard title="Фильтрация под вас" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                Найдите роль под свои возможности.
                <x-slot:figure>
                    <img src="/images/pros-1.png" alt="Фильтрация ролей" />
                </x-slot:figure>
            </x-marycard>

            <x-marycard title="Удобство использования" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
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
<section
    class="mx-auto w-full max-w-7xl space-y-10"
    data-test="home-dashboard"
    aria-labelledby="dashboard-heading"
>
    <header class="space-y-2">
        <h1 id="dashboard-heading" class="font-display text-3xl font-bold sm:text-4xl">Краткая сводка</h1>
        <p class="text-base-content/80">
            Здравствуйте, {{ auth()->user()->fio }}.
        </p>
    </header>

    @if ($showPhoneVerificationBanner)
        <div role="alert" class="alert alert-warning shadow-sm" data-test="dashboard-phone-banner">
            <span>Подтвердите номер телефона, чтобы пользоваться всеми функциями.</span>
            <a href="{{ route('phone.verify.notice') }}" class="btn btn-sm btn-neutral">Подтвердить</a>
        </div>
    @endif

    @if ($unreadNotificationsCount > 0)
        <p class="text-sm text-base-content/80" data-test="dashboard-unread-notifications">
            Непрочитанных уведомлений: <span class="font-semibold tabular-nums">{{ $unreadNotificationsCount }}</span>
            (список в шапке сайта).
        </p>
    @endif

    @if (auth()->user()->isParticipant())
        @if ($participantNextStepTitle !== '')
            <div class="rounded-xl border border-primary/20 bg-primary/10 p-4 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-primary/80">Ваш следующий шаг</p>
                <p class="mt-1 font-semibold">{{ $participantNextStepTitle }}</p>
                <p class="mt-1 text-sm text-base-content/80">{{ $participantNextStepHint }}</p>
                @if ($participantNextStepHref && $participantNextStepLabel)
                    <a href="{{ $participantNextStepHref }}" class="btn btn-primary btn-sm mt-3">{{ $participantNextStepLabel }}</a>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                <x-slot:menu>
                    <a href="/profile/teams#pending-team-role-applications" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Заявки команд на хакатоны (ожидают)" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $pendingHackatonApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По вашим командам, ожидающие решения организатора.</p>
                @if ($pendingHackatonApplicationsCount > 0)
                    <x-slot:menu>
                        <a href="/hackatons" class="btn btn-ghost btn-sm">Каталог</a>
                    </x-slot:menu>
                @endif
            </x-marycard>
        </div>

        @if (count($hackatonApplicationsPreview) > 0)
            <x-marycard title="Заявки команд на хакатоны" class="card card-border bg-base-100 shadow-sm w-full max-w-2xl">
                <ul class="space-y-2">
                    @foreach ($hackatonApplicationsPreview as $row)
                        <li class="flex flex-wrap items-baseline justify-between gap-2 border-b border-base-200 pb-2 last:border-0">
                            <div>
                                <a href="{{ route('hackatons.show', $row['hackaton_id']) }}#participant-hackaton-applications" class="link link-primary font-medium">{{ $row['title'] }}</a>
                                <span class="text-sm text-base-content/70"> — {{ $row['team_title'] }}</span>
                            </div>
                            <span class="badge badge-warning badge-sm">{{ $row['status_label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-marycard>
        @endif

        @if (count($participantHackatonsPreview) > 0)
            <x-marycard title="Хакатоны (ваши и ближайшие публичные)" class="card card-border bg-base-100 shadow-sm w-full max-w-2xl">
                <ul class="space-y-2">
                    @foreach ($participantHackatonsPreview as $row)
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
                @if ($pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId)
                    <x-slot:menu>
                        <a href="{{ route('hackatons.show', $organizerFirstPendingHackatonId) }}?applications_status=pending#organizer-team-applications" class="btn btn-ghost btn-sm">Рассмотреть</a>
                    </x-slot:menu>
                @endif
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            @if ($pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId)
                <a href="{{ route('hackatons.show', $organizerFirstPendingHackatonId) }}?applications_status=pending#organizer-team-applications" class="btn btn-primary">Рассмотреть заявки</a>
            @endif
            <a href="/profile/hackatons" class="btn {{ $pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId ? 'btn-outline' : 'btn-primary' }}">Мои хакатоны</a>
            <a href="/hackatons/create" class="btn btn-outline">Создать хакатон</a>
            <a href="/hackatons" class="btn btn-outline">Каталог хакатонов</a>
        </div>
    @elseif (auth()->user()->isJudge())
        @if ($judgeHackatonsCount === 0)
            <div class="rounded-xl border border-base-200 bg-base-100 p-6 text-center shadow-sm" data-test="judge-dashboard-empty">
                <p class="text-base-content/80">У вас пока нет назначенных хакатонов. Когда организатор добавит вас в судьи, события появятся здесь.</p>
                <a href="/hackatons" class="btn btn-primary mt-4">Каталог хакатонов</a>
            </div>
        @else
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
                            <li class="flex flex-wrap items-center justify-between gap-2 border-b border-base-200 pb-2 last:border-0">
                                <div class="flex min-w-0 flex-1 flex-wrap items-baseline gap-2">
                                    <a href="{{ route('hackatons.show', $row['id']) }}" class="link link-primary font-medium">{{ $row['title'] }}</a>
                                    @if ($row['start_at'])
                                        <span class="text-sm text-base-content/70">{{ $row['start_at'] }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('hackatons.show', $row['id']) }}#hackaton-cases" class="btn btn-ghost btn-xs shrink-0">К кейсам</a>
                            </li>
                        @endforeach
                    </ul>
                </x-marycard>
            @endif
        @endif
        <div class="flex flex-wrap gap-3">
            <a href="/hackatons" class="btn btn-primary">Все хакатоны</a>
            <a href="/profile" class="btn btn-outline">Профиль</a>
        </div>
    @elseif (auth()->user()->isAdmin())
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-marycard title="Пользователей" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $usersCount }}</p>
            </x-marycard>
            <x-marycard title="Хакатонов" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminHackatonsCount }}</p>
            </x-marycard>
            <x-marycard title="Партнёров" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminPartnersCount }}</p>
            </x-marycard>
            <x-marycard title="Заявок команд на рассмотрении" class="card card-border bg-base-100 shadow-sm">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminPendingApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По всей платформе.</p>
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
</section>
@endauth
