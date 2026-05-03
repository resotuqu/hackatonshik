<?php

use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\Team;
use App\Models\User;
use App\ViewModels\HomeDashboardData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Главная'])] class extends Component {
    /** @var array<int, Hackaton> */
    public array $featuredHackatons = [];

    /** @var array<int, Team> */
    public array $featuredTeams = [];

    public int $publicActiveHackatonsCount = 0;

    public int $publicParticipantsCount = 0;

    public int $publicTeamsCount = 0;

    /** @var list<array{path: string, alt: string}> */
    public array $homeCarouselImages = [];

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
        $this->homeCarouselImages = [
            ['path' => url('/logo.svg'), 'alt' => 'Хакатонщик'],
            ['path' => url('/hackatonshik.svg'), 'alt' => 'Платформа для хакатонов'],
            ['path' => url('/logo.svg'), 'alt' => 'Сообщество участников'],
        ];

        $this->featuredHackatons = Hackaton::query()
            ->where('is_public', true)
            ->whereIn('status', [
                HackatonStatus::PUBLISHED,
                HackatonStatus::REGISTRATION_OPEN,
                HackatonStatus::IN_PROGRESS,
                HackatonStatus::JUDGING,
            ])
            ->latest('start_at')
            ->limit(4)
            ->get()
            ->all();

        $this->featuredTeams = Team::query()
            ->with(['hackaton:id,title,status', 'roles:id,team_id,user_id'])
            ->whereHas('hackaton', fn ($query) => $query->where('is_public', true))
            ->latest('updated_at')
            ->limit(4)
            ->get()
            ->all();

        $totals = Cache::remember('home-public-totals', now()->addMinutes(10), function (): array {
            $activeHackatons = Hackaton::query()
                ->where('is_public', true)
                ->whereIn('status', [HackatonStatus::REGISTRATION_OPEN, HackatonStatus::IN_PROGRESS, HackatonStatus::JUDGING])
                ->get();

            return [
                'active_hackatons' => $activeHackatons->count(),
                'participants' => $activeHackatons->sum(fn (Hackaton $hackaton) => $hackaton->participantsCount()),
                'teams' => $activeHackatons->sum(fn (Hackaton $hackaton) => $hackaton->teamsCount()),
            ];
        });

        $this->publicActiveHackatonsCount = (int) ($totals['active_hackatons'] ?? 0);
        $this->publicParticipantsCount = (int) ($totals['participants'] ?? 0);
        $this->publicTeamsCount = (int) ($totals['teams'] ?? 0);

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
    <section id="start" class="relative overflow-hidden rounded-3xl border border-primary/20 bg-base-100 px-4 py-12 shadow-lg shadow-primary/5 sm:py-16">
        <div class="pointer-events-none absolute inset-0 opacity-90" aria-hidden="true" style="background: radial-gradient(1200px 600px at 10% -10%, oklch(56% 0.21 272 / 0.28), transparent 55%), radial-gradient(900px 500px at 90% 20%, oklch(82% 0.19 118 / 0.18), transparent 50%), radial-gradient(600px 400px at 50% 100%, oklch(22% 0.06 264 / 0.35), transparent 45%);"></div>
        <div class="relative">
            <div class="mx-auto max-w-4xl text-center">
                <h1 class="font-display text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
                    Найдите команду. Проведите хакатон.
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-base-content/80">
                    Хакатонщик помогает участникам, командам и организаторам на всём пути — от поиска единомышленников до финальной защиты и вручения сертификатов.
                </p>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <a href="/teams" class="btn btn-primary btn-lg">Найти команду</a>
                    <a href="/hackatons/create" class="btn btn-secondary btn-lg">Создать хакатон</a>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="font-display text-3xl font-bold">Активные хакатоны</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse ($featuredHackatons as $hackaton)
                <article class="rounded-2xl border border-base-300 bg-base-100 p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ route('hackatons.show', $hackaton) }}" class="font-semibold link link-hover">{{ $hackaton->title }}</a>
                        <span class="badge badge-primary badge-outline">{{ $hackaton->status?->label() }}</span>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <p class="rounded-lg bg-base-200 px-3 py-2">Команд: <span class="font-medium">{{ $hackaton->teamsCount() }}</span></p>
                        <p class="rounded-lg bg-base-200 px-3 py-2">Участников: <span class="font-medium">{{ $hackaton->participantsCount() }}</span></p>
                    </div>
                </article>
            @empty
                <p class="text-base-content/70">Активные хакатоны скоро появятся.</p>
            @endforelse
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="font-display text-3xl font-bold">Популярные команды</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @forelse ($featuredTeams as $team)
                <article class="rounded-2xl border border-base-300 bg-base-100 p-4 shadow-sm">
                    <a href="{{ route('teams.show', $team) }}" class="font-semibold link link-hover">{{ $team->title }}</a>
                    <p class="mt-1 text-sm text-base-content/70">{{ $team->hackaton?->title }}</p>
                    <p class="mt-3 text-sm">Участников в команде: <span class="font-medium">{{ $team->participantsCount() }}</span></p>
                </article>
            @empty
                <p class="text-base-content/70">Публичные команды скоро появятся.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-base-300 bg-base-100 p-6" x-data="{ active: 0, participants: 0, teams: 0 }" x-init="const animate=(key,target)=>{let current=0;const step=Math.max(1,Math.ceil(target/45));const timer=setInterval(()=>{current+=step;if(current>=target){current=target;clearInterval(timer);} if(key==='active'){active=current;} if(key==='participants'){participants=current;} if(key==='teams'){teams=current;}}, 24)}; animate('active', {{ $publicActiveHackatonsCount }}); animate('participants', {{ $publicParticipantsCount }}); animate('teams', {{ $publicTeamsCount }});">
        <h2 class="font-display text-3xl font-bold">Платформа в цифрах</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-base-200 p-4 text-center">
                <p class="text-sm text-base-content/70">Активных хакатонов</p>
                <p class="text-3xl font-bold tabular-nums" x-text="active"></p>
            </div>
            <div class="rounded-xl bg-base-200 p-4 text-center">
                <p class="text-sm text-base-content/70">Участников</p>
                <p class="text-3xl font-bold tabular-nums" x-text="participants"></p>
            </div>
            <div class="rounded-xl bg-base-200 p-4 text-center">
                <p class="text-sm text-base-content/70">Команд</p>
                <p class="text-3xl font-bold tabular-nums" x-text="teams"></p>
            </div>
        </div>
    </section>

    <livewire:home-how-it-works />

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <article class="rounded-2xl border border-base-300 bg-base-100 p-6 shadow-sm">
            <h2 class="font-display text-2xl font-bold">Отзывы участников</h2>
            <div class="mt-4 space-y-3">
                <blockquote class="rounded-lg border border-base-300 bg-base-200/60 p-3 text-sm">«Нашли двух разработчиков за один вечер и подали заявку за 10 минут.»</blockquote>
                <blockquote class="rounded-lg border border-base-300 bg-base-200/60 p-3 text-sm">«Удобно следить за дедлайнами и анонсами без десятка чатов.»</blockquote>
                <blockquote class="rounded-lg border border-base-300 bg-base-200/60 p-3 text-sm">«Организаторам стало проще модерировать команды и заявки.»</blockquote>
            </div>
        </article>
        <article class="rounded-2xl border border-base-300 bg-base-100 p-4 shadow-sm">
            <x-image-carousel
                carousel-id="home-hero-carousel"
                :items="$homeCarouselImages"
                aspect-class="aspect-[16/10]"
                empty-text="Изображения появятся позже"
            />
        </article>
    </section>

    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    'name' => 'Хакатонщик',
                    'url' => url('/'),
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => url('/hackatons').'?q={search_term_string}',
                        'query-input' => 'required name=search_term_string',
                    ],
                ],
                [
                    '@type' => 'Organization',
                    'name' => 'Хакатонщик',
                    'url' => url('/'),
                    'logo' => url('/logo.svg'),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
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
