<div>
@guest
<div class="mx-auto w-full max-w-7xl space-y-[var(--spacing-section)]">
    {{-- Hero --}}
    <section
        id="start"
        class="relative overflow-hidden rounded-card border border-base-300 bg-base-100 lg:min-h-[30rem]"
    >
        <div class="relative flex min-h-[inherit] flex-col gap-8 px-5 py-10 sm:gap-10 sm:px-8 sm:py-14 lg:flex-row lg:items-center lg:gap-16 lg:px-12 lg:py-16">
            <div class="flex max-w-xl flex-1 flex-col justify-center lg:max-w-[min(36rem,50%)] lg:text-left">
                <h1 class="ui-heading-display text-4xl font-bold leading-[1.08] sm:text-5xl lg:text-6xl">
                    {{ __('ui.home.hero_title') }}
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-relaxed text-base-content/70 sm:text-lg">
                    {{ __('ui.home.hero_subtitle') }}
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center lg:justify-start">
                    <a
                        href="{{ route('register', ['type' => 'partner']) }}"
                        class="ui-cta-secondary btn-lg order-1 text-base sm:text-lg sm:order-2"
                    >
                        {{ __('ui.home.become_organizer') }}
                    </a>
                    <a href="{{ route('teams.index') }}" class="ui-cta-outline-primary btn-lg order-2 sm:order-1">
                        {{ __('ui.home.find_team') }}
                    </a>
                </div>
            </div>
            <div class="relative flex max-w-[18rem] flex-1 shrink-0 items-center justify-center self-center sm:max-w-[20rem] lg:max-w-[min(24rem,40%)] lg:justify-end">
                <div class="relative aspect-square w-full max-w-full">
                    <div class="relative flex h-full w-full items-center justify-center rounded-card border border-base-300 bg-base-200 p-4 sm:p-5">
                        <img
                            src="{{ url('/hackatonshik.svg') }}"
                            alt="{{ __('ui.home.hero_alt') }}"
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
            <h2 class="ui-heading-display text-3xl font-bold sm:text-4xl">{{ __('ui.home.active_hackatons') }}</h2>
            <a href="{{ route('hackatons.index') }}" class="btn btn-ghost btn-sm gap-2 sm:btn-md">
                <x-app-icon icon="heroicons:arrow-right" class="h-4 w-4" />
                {{ __('ui.home.all_hackatons') }}
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
                        :actionHref="route('hackatons.index')"
                        :actionLabel="__('ui.home.open_catalog')"
                    />
                </div>
            @endforelse
        </div>
    </section>

    {{-- Платформа в цифрах --}}
    <section
        id="home-stats"
        class="rounded-card border border-base-300 bg-base-100 p-6 sm:p-8"
    >
        <h2 class="ui-heading-display text-3xl font-bold sm:text-4xl">{{ __('ui.home.stats_title') }}</h2>
        <p class="mt-2 max-w-2xl text-base-content/70">{{ __('ui.home.stats_description') }}</p>
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-3 sm:gap-6">
            <div class="ui-stat-tile p-6 text-center sm:p-7">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                    <x-app-icon icon="heroicons:trophy" class="h-7 w-7" label="{{ __('ui.home.stats_hackatons') }}" />
                </div>
                <p class="text-base font-medium text-base-content/90">{{ __('ui.home.stats_hackatons') }}</p>
                <p class="mt-2 text-4xl font-semibold tabular-nums text-base-content sm:text-5xl">{{ $publicHackatonsCount }}</p>
            </div>
            <div class="ui-stat-tile p-6 text-center sm:p-7">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                    <x-app-icon icon="heroicons:users" class="h-7 w-7" label="{{ __('ui.home.stats_participants') }}" />
                </div>
                <p class="text-base font-medium text-base-content/90">{{ __('ui.home.stats_participants') }}</p>
                <p class="mt-2 text-4xl font-semibold tabular-nums text-base-content sm:text-5xl">{{ $publicParticipantsCount }}</p>
            </div>
            <div class="ui-stat-tile p-6 text-center sm:p-7">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-base-200 text-base-content/60">
                    <x-app-icon icon="heroicons:user-group" class="h-7 w-7" label="{{ __('ui.home.stats_teams') }}" />
                </div>
                <p class="text-base font-medium text-base-content/90">{{ __('ui.home.stats_teams') }}</p>
                <p class="mt-2 text-4xl font-semibold tabular-nums text-base-content sm:text-5xl">{{ $publicTeamsCount }}</p>
            </div>
        </div>
    </section>

    <livewire:home-how-it-works />

    <div class="mt-14 flex justify-center border-t border-base-300 px-4 pb-2 pt-10 sm:mt-16 sm:pt-12">
        <a href="{{ route('home') }}" class="block w-1/2 max-w-[50%] shrink-0" aria-label="{{ __('ui.home.brand_home') }}">
            <img
                src="{{ url('/logo_white.svg') }}"
                onerror="this.onerror=null;this.src='{{ url('/logo.svg') }}';"
                alt="{{ __('ui.auth.brand_name') }}"
                class="block h-auto w-full object-contain group-data-[theme=cmyk]:hidden"
                loading="lazy"
                decoding="async"
            />
            <img
                src="{{ url('/logo_black.svg') }}"
                onerror="this.onerror=null;this.src='{{ url('/logo.svg') }}';"
                alt="{{ __('ui.auth.brand_name') }}"
                class="hidden h-auto w-full object-contain group-data-[theme=cmyk]:block"
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
@php
    $dashboardUser = auth()->user();
    $dashboardHeading = $this->availableDashboardRoles($dashboardUser)[$activeDashboardRole] ?? __('ui.dashboard.summary');
@endphp
<section
    class="mx-auto w-full max-w-7xl space-y-10"
    data-test="home-dashboard"
    aria-labelledby="dashboard-heading"
>
    <header class="flex flex-col gap-1">
        <h1 id="dashboard-heading" class="ui-heading-display text-3xl font-bold sm:text-4xl">{{ $dashboardHeading }}</h1>
        <p class="text-base-content/70 font-medium">
            {{ __('ui.dashboard.greeting', ['name' => $dashboardUser->fio]) }}
        </p>
    </header>

    @if ($showPhoneVerificationBanner)
        <div role="alert" class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-base-300 bg-base-100 px-4 py-3" data-test="dashboard-phone-banner">
            <span class="text-sm">{{ __('ui.dashboard.phone_banner') }}</span>
            <a href="{{ route('phone.verify.notice') }}" class="btn btn-sm btn-neutral">{{ __('ui.dashboard.phone_verify') }}</a>
        </div>
    @endif

    @if ($unreadNotificationsCount > 0)
        <p class="text-sm text-base-content/80" data-test="dashboard-unread-notifications">
            {{ __('ui.dashboard.unread_notifications', ['count' => $unreadNotificationsCount]) }}
        </p>
    @endif

    @include('pages.home.dashboard')
</section>
@endauth
</div>
