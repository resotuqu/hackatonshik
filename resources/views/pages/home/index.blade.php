@guest
<div class="mx-auto w-full max-w-7xl space-y-[var(--spacing-section)]">
    {{-- Hero --}}
    <section
        id="start"
        class="relative overflow-hidden rounded-[var(--radius-card)] border border-base-300 bg-base-100 lg:min-h-[30rem]"
    >
        <div class="relative flex min-h-[inherit] flex-col gap-8 px-5 py-10 sm:gap-10 sm:px-8 sm:py-14 lg:flex-row lg:items-center lg:gap-16 lg:px-12 lg:py-16">
            <div class="flex max-w-xl flex-1 flex-col justify-center lg:max-w-[min(36rem,50%)] lg:text-left">
                <h1 class="ui-heading-display text-4xl font-bold leading-[1.08] sm:text-5xl lg:text-6xl">
                    Найдите команду. Проведите хакатон.
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-relaxed text-base-content/70 sm:text-lg">
                    Хакатонщик помогает участникам, командам и организаторам пройти весь путь — от поиска единомышленников до финальной защиты и вручения сертификатов.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center lg:justify-start">
                    <a
                        href="/contacts"
                        class="ui-cta-secondary btn-lg order-1 text-base sm:text-lg sm:order-2"
                    >
                        Стать организатором
                    </a>
                    <a href="/teams" class="ui-cta-outline-primary btn-lg order-2 sm:order-1">
                        Найти команду
                    </a>
                </div>
            </div>
            <div class="relative flex max-w-[18rem] flex-1 shrink-0 items-center justify-center self-center sm:max-w-[20rem] lg:max-w-[min(24rem,40%)] lg:justify-end">
                <div class="relative aspect-square w-full max-w-full">
                    <div class="relative flex h-full w-full items-center justify-center rounded-[var(--radius-card)] border border-base-300 bg-base-200 p-4 sm:p-5">
                        <img
                            src="{{ url('/hackatonshik.svg') }}"
                            alt=""
                            class="h-auto w-full max-h-[min(18rem,42vh)] object-contain sm:max-h-[min(20rem,48vh)] lg:max-h-[min(22rem,50vh)]"
                            width="480"
                            height="480"
                            loading="eager"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Активные хакатоны --}}
    <section class="space-y-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h2 class="ui-heading-display text-3xl font-bold sm:text-4xl">Активные хакатоны</h2>
            <a href="/hackatons" class="btn btn-ghost btn-sm gap-2 sm:btn-md">
                <x-app-icon icon="heroicons:arrow-right" class="h-4 w-4" />
                Все хакатоны
            </a>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            @forelse ($featuredHackatons as $hackaton)
                <x-hackaton-card
                    :hackaton="$hackaton"
                    :can-quick-apply="false"
                    href="{{ route('hackatons.show', $hackaton) }}"
                />
            @empty
                <div class="sm:col-span-2">
                    <x-empty-state
                        :title="__('ui.home.featured_empty_title')"
                        :description="__('ui.home.featured_empty_description')"
                        icon="heroicons:rocket-launch"
                        :actionHref="'/hackatons'"
                        :actionLabel="__('ui.home.open_catalog')"
                    />
                </div>
            @endforelse
        </div>
    </section>

    {{-- Платформа в цифрах --}}
    <section
        id="home-stats"
        class="rounded-[var(--radius-card)] border border-base-300 bg-base-100 p-6 sm:p-8"
    >
        <h2 class="ui-heading-display text-3xl font-bold sm:text-4xl">Платформа в цифрах</h2>
        <p class="mt-2 max-w-2xl text-base-content/70">Учитываются все публичные хакатоны на платформе — текущие, предстоящие, завершённые и в архиве (кроме черновиков).</p>
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-3 sm:gap-6">
            <div class="ui-stat-tile p-6 text-center sm:p-7">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                    <x-app-icon icon="heroicons:trophy" class="h-7 w-7" label="Хакатоны на платформе" />
                </div>
                <p class="text-base font-medium text-base-content/90">Хакатонов</p>
                <p class="mt-2 text-4xl font-semibold tabular-nums text-base-content sm:text-5xl">{{ $publicHackatonsCount }}</p>
            </div>
            <div class="ui-stat-tile p-6 text-center sm:p-7">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                    <x-app-icon icon="heroicons:users" class="h-7 w-7" label="Участники" />
                </div>
                <p class="text-base font-medium text-base-content/90">Участников</p>
                <p class="mt-2 text-4xl font-semibold tabular-nums text-base-content sm:text-5xl">{{ $publicParticipantsCount }}</p>
            </div>
            <div class="ui-stat-tile p-6 text-center sm:p-7">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                    <x-app-icon icon="heroicons:user-group" class="h-7 w-7" label="Команды" />
                </div>
                <p class="text-base font-medium text-base-content/90">Команд</p>
                <p class="mt-2 text-4xl font-semibold tabular-nums text-base-content sm:text-5xl">{{ $publicTeamsCount }}</p>
            </div>
        </div>
    </section>

    <livewire:home-how-it-works />

    <div class="mt-14 flex justify-center border-t border-base-300 px-4 pb-2 pt-10 sm:mt-16 sm:pt-12">
        <a href="{{ route('home') }}" class="block w-1/2 max-w-[50%] shrink-0" aria-label="Хакатонщик — на главную">
            <img
                src="{{ url('/logo_white.svg') }}"
                onerror="this.onerror=null;this.src='{{ url('/logo.svg') }}';"
                alt="Хакатонщик"
                class="block h-auto w-full object-contain group-data-[theme=hackatonshik-light]:hidden"
                loading="lazy"
                decoding="async"
            />
            <img
                src="{{ url('/logo_black.svg') }}"
                onerror="this.onerror=null;this.src='{{ url('/logo.svg') }}';"
                alt="Хакатонщик"
                class="hidden h-auto w-full object-contain group-data-[theme=hackatonshik-light]:block"
                loading="lazy"
                decoding="async"
            />
        </a>
    </div>

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
    <header class="flex flex-col gap-1">
        <h1 id="dashboard-heading" class="ui-heading-display text-3xl font-bold sm:text-4xl">Краткая сводка</h1>
        <p class="text-base-content/60 font-medium">
            Здравствуйте, <span class="text-base-content font-bold">{{ auth()->user()->fio }}</span>. Рады вас видеть!
        </p>
    </header>

    @if ($showPhoneVerificationBanner)
        <div role="alert" class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-base-300 bg-base-100 px-4 py-3" data-test="dashboard-phone-banner">
            <span class="text-sm">Подтвердите номер телефона, чтобы пользоваться всеми функциями.</span>
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
        <div id="participant-hackaton-dashboard" class="scroll-mt-24 space-y-10">
        <x-recommended-teams :recommendations="$recommendedTeams" />
        @if ($participantNextStepTitle !== '')
            <div class="rounded-lg border border-base-300 bg-base-100 p-4">
                <p class="text-xs font-medium uppercase tracking-wide text-base-content/50">Ваш следующий шаг</p>
                <p class="mt-1 font-semibold">{{ $participantNextStepTitle }}</p>
                <p class="mt-1 text-sm text-base-content/70">{{ $participantNextStepHint }}</p>
                @if ($participantNextStepHref && $participantNextStepLabel)
                    <a href="{{ $participantNextStepHref }}" class="btn btn-neutral btn-sm mt-3">{{ $participantNextStepLabel }}</a>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="ui-surface-card p-5 group">
                <div class="flex flex-col h-full justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-base-content/50">Мои команды</span>
                        <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                            <x-app-icon icon="heroicons:user-group" class="h-4 w-4" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="ui-heading-display text-4xl font-semibold tabular-nums">{{ $teamsCount }}</p>
                    </div>
                    <a href="/profile/teams" class="ui-cta-ghost btn-xs mt-4 self-end">Открыть</a>
                </div>
            </div>

            <div class="ui-surface-card p-5 group">
                <div class="flex flex-col h-full justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-base-content/50">Сертификаты</span>
                        <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                            <x-app-icon icon="heroicons:academic-cap" class="h-4 w-4" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="ui-heading-display text-4xl font-semibold tabular-nums">{{ $certificatesCount }}</p>
                    </div>
                    <a href="/profile/certificates" class="ui-cta-ghost btn-xs mt-4 self-end">Открыть</a>
                </div>
            </div>

            <div class="ui-surface-card p-5 group">
                <div class="flex flex-col h-full justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-base-content/50">Заявки в команды</span>
                        <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                            <x-app-icon icon="heroicons:envelope" class="h-4 w-4" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="ui-heading-display text-4xl font-semibold tabular-nums">{{ $pendingTeamApplicationsCount }}</p>
                    </div>
                    <a href="/profile/teams#pending-team-role-applications" class="ui-cta-ghost btn-xs mt-4 self-end">Открыть</a>
                </div>
            </div>

            <div class="ui-surface-card p-5 group">
                <div class="flex flex-col h-full justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-base-content/50">Заявки на хакатоны</span>
                        <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                            <x-app-icon icon="heroicons:rocket-launch" class="h-4 w-4" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="ui-heading-display text-4xl font-semibold tabular-nums">{{ $pendingHackatonApplicationsCount }}</p>
                    </div>
                    <a href="/hackatons" class="ui-cta-ghost btn-xs mt-4 self-end">Каталог</a>
                </div>
            </div>
        </div>

        @if (count($hackatonApplicationsPreview) > 0)
            <x-marycard title="Заявки команд на хакатоны" class="card border border-base-300 bg-base-100 w-full max-w-2xl">
                <ul class="space-y-2">
                    @foreach ($hackatonApplicationsPreview as $row)
                        <li class="flex flex-wrap items-baseline justify-between gap-2 border-b border-base-300 pb-2 last:border-0">
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
            <x-marycard title="Хакатоны (ваши и ближайшие публичные)" class="card border border-base-300 bg-base-100 w-full max-w-2xl">
                <ul class="space-y-2">
                    @foreach ($participantHackatonsPreview as $row)
                        <li class="flex flex-wrap items-baseline justify-between gap-2 border-b border-base-300 pb-2 last:border-0">
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
            <a href="{{ route('participant.hackatons') }}" class="btn btn-primary">Мои заявки и хакатоны</a>
            <a href="{{ route('hackatons.index') }}" class="btn btn-outline">Каталог</a>
            <a href="{{ route('teams.create') }}" class="btn btn-outline">Создать команду</a>
            <a href="{{ route('profile.teams') }}" class="btn btn-outline">Мои команды</a>
        </div>
        </div>
    @endif

    @if (auth()->user()->isOrganizer())
        <section class="space-y-6" data-test="home-organizer-dashboard">
            <h2 class="ui-heading-display text-xl font-semibold">Организатор</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-marycard title="Мои хакатоны" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $hackatonsCount }}</p>
                <x-slot:menu>
                    <a href="{{ route('organizer.dashboard') }}" class="btn btn-ghost btn-sm">Открыть</a>
                </x-slot:menu>
            </x-marycard>
            <x-marycard title="Заявки команд на рассмотрении" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $pendingHackatonApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По всем вашим хакатонам.</p>
                @if ($pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId)
                    <x-slot:menu>
                        <a href="{{ route('hackatons.show', $organizerFirstPendingHackatonId) }}?applications_status=pending#hackaton-tab-participants" class="btn btn-ghost btn-sm">Рассмотреть</a>
                    </x-slot:menu>
                @endif
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            @if ($pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId)
                <a href="{{ route('hackatons.show', $organizerFirstPendingHackatonId) }}?applications_status=pending#hackaton-tab-participants" class="btn btn-primary">Рассмотреть заявки</a>
            @endif
            <a href="{{ route('organizer.dashboard') }}" class="btn {{ $pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId ? 'btn-outline' : 'btn-primary' }}">Мои хакатоны</a>
            <a href="{{ route('hackatons.create') }}" class="btn btn-outline">Создать хакатон</a>
            <a href="{{ route('hackatons.index') }}" class="btn btn-outline">Каталог хакатонов</a>
        </div>
        </section>
    @endif

    @if (auth()->user()->isJudge())
        <section class="space-y-6" data-test="home-judge-dashboard">
            <h2 class="ui-heading-display text-xl font-semibold">Судья</h2>
        @if ($judgeHackatonsCount === 0)
            <div class="rounded-lg border border-base-300 bg-base-100 p-6 text-center" data-test="judge-dashboard-empty">
                <p class="text-base-content/70">У вас пока нет назначенных хакатонов. Когда организатор добавит вас в судьи, события появятся здесь.</p>
                <a href="{{ route('hackatons.index') }}" class="btn btn-neutral mt-4">Каталог хакатонов</a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-marycard title="Назначенные хакатоны" class="card border border-base-300 bg-base-100">
                    <p class="text-3xl font-semibold tabular-nums">{{ $judgeHackatonsCount }}</p>
                    <x-slot:menu>
                        <a href="{{ route('judge.dashboard') }}" class="btn btn-ghost btn-sm">Панель судьи</a>
                    </x-slot:menu>
                </x-marycard>
            </div>
            @if (count($judgeHackatonsPreview) > 0)
                <x-marycard title="Ближайшие по дате начала" class="card border border-base-300 bg-base-100 w-full max-w-2xl">
                    <ul class="space-y-2">
                        @foreach ($judgeHackatonsPreview as $row)
                            <li class="flex flex-wrap items-center justify-between gap-2 border-b border-base-300 pb-2 last:border-0">
                                <div class="flex min-w-0 flex-1 flex-wrap items-baseline gap-2">
                                    <a href="{{ route('hackatons.show', $row['id']) }}" class="link link-primary font-medium">{{ $row['title'] }}</a>
                                    @if ($row['start_at'])
                                        <span class="text-sm text-base-content/70">{{ $row['start_at'] }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('judge.hackatons.show', $row['id']) }}" class="btn btn-ghost btn-xs shrink-0">К оценке</a>
                            </li>
                        @endforeach
                    </ul>
                </x-marycard>
            @endif
        @endif
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('judge.dashboard') }}" class="btn btn-primary">Панель судьи</a>
            <a href="{{ route('hackatons.index') }}" class="btn btn-outline">Каталог</a>
        </div>
        </section>
    @endif

    @if (auth()->user()->isAdmin())
        <section class="space-y-6" data-test="home-admin-dashboard">
            <h2 class="ui-heading-display text-xl font-semibold">Администратор</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-marycard title="Пользователей" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $usersCount }}</p>
            </x-marycard>
            <x-marycard title="Хакатонов" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminHackatonsCount }}</p>
            </x-marycard>
            <x-marycard title="Партнёров" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminPartnersCount }}</p>
            </x-marycard>
            <x-marycard title="Заявок команд на рассмотрении" class="card border border-base-300 bg-base-100">
                <p class="text-3xl font-semibold tabular-nums">{{ $adminPendingApplicationsCount }}</p>
                <p class="mt-2 text-sm text-base-content/70">По всей платформе.</p>
            </x-marycard>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Админ-панель</a>
            <a href="{{ route('hackatons.index') }}" class="btn btn-outline">Хакатоны</a>
        </div>
        </section>
    @endif

    @if (! auth()->user()->isParticipant() && ! auth()->user()->isOrganizer() && ! auth()->user()->isJudge() && ! auth()->user()->isAdmin())
        <p class="text-base-content/80">Выберите раздел в меню слева или перейдите к хакатонам.</p>
        <div class="flex flex-wrap gap-3">
            <a href="/hackatons" class="btn btn-primary">Хакатоны</a>
            <a href="/profile" class="btn btn-outline">Профиль</a>
        </div>
    @endif
</section>
    @include('pages.home.dashboard')
@endauth
