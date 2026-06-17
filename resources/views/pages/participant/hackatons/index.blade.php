<div class="mx-auto w-full max-w-6xl space-y-8">
    <nav class="text-sm breadcrumbs">
        <ul>
            <li><a href="{{ route('home') }}">Главная</a></li>
            <li><a href="{{ route('profile') }}">Профиль</a></li>
            <li class="opacity-70">Мои заявки и хакатоны</li>
        </ul>
    </nav>

    <x-profile-nav-tabs active="hackatons" />

    <header class="space-y-2">
        <h1 class="ui-heading-display text-3xl font-bold sm:text-4xl">Мои заявки и хакатоны</h1>
        <p class="text-base-content/70">Заявки ваших команд, участие в событиях и быстрые переходы в рабочие пространства.</p>
    </header>

    @if ($participantNextStepTitle !== '')
        <div class="rounded-xl border border-primary/20 bg-primary/10 p-4 shadow-sm">
            <p class="text-xs text-primary/80">Ваш следующий шаг</p>
            <p class="mt-1 font-semibold">{{ $participantNextStepTitle }}</p>
            <p class="mt-1 text-sm text-base-content/80">{{ $participantNextStepHint }}</p>
            @if ($participantNextStepHref && $participantNextStepLabel)
                <a href="{{ $participantNextStepHref }}" class="btn btn-primary btn-sm mt-3">{{ $participantNextStepLabel }}</a>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="ui-surface-card p-5">
            <p class="text-xs text-base-content/50">Мои команды</p>
            <p class="ui-heading-display mt-2 text-4xl font-semibold tabular-nums">{{ $teamsCount }}</p>
            <a href="{{ route('profile.teams') }}" class="ui-cta-ghost btn-xs mt-4">Открыть</a>
        </div>
        <div class="ui-surface-card p-5">
            <p class="text-xs text-base-content/50">Сертификаты</p>
            <p class="ui-heading-display mt-2 text-4xl font-semibold tabular-nums">{{ $certificatesCount }}</p>
            <a href="{{ route('profile.certificates') }}" class="ui-cta-ghost btn-xs mt-4">Открыть</a>
        </div>
        <div class="ui-surface-card p-5">
            <p class="text-xs text-base-content/50">Заявки в команды</p>
            <p class="ui-heading-display mt-2 text-4xl font-semibold tabular-nums">{{ $pendingTeamApplicationsCount }}</p>
            <a href="{{ route('profile.teams') }}#pending-team-role-applications" class="ui-cta-ghost btn-xs mt-4">Открыть</a>
        </div>
        <div class="ui-surface-card p-5">
            <p class="text-xs text-base-content/50">Заявки на хакатоны</p>
            <p class="ui-heading-display mt-2 text-4xl font-semibold tabular-nums">{{ $pendingHackatonApplicationsCount }}</p>
            <a href="{{ route('hackatons.index') }}" class="ui-cta-ghost btn-xs mt-4">Каталог</a>
        </div>
    </div>

    @if (count($hackatonApplicationsPreview) > 0)
        <x-marycard title="Заявки команд на хакатоны" class="card card-border bg-base-100 shadow-sm w-full">
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

    @if (count($participantHackatons) > 0)
        <x-marycard title="Хакатоны" class="card card-border bg-base-100 shadow-sm w-full">
            <ul class="space-y-2">
                @foreach ($participantHackatons as $row)
                    <li class="flex flex-wrap items-center justify-between gap-2 border-b border-base-200 pb-2 last:border-0">
                        <div class="flex min-w-0 flex-1 flex-wrap items-baseline gap-2">
                            <a href="{{ route('hackatons.show', $row['id']) }}" class="link link-primary font-medium">{{ $row['title'] }}</a>
                            @if ($row['start_at'])
                                <span class="text-sm text-base-content/70">{{ $row['start_at'] }}</span>
                            @endif
                        </div>
                        @if ($row['hub_url'])
                            <a href="{{ $row['hub_url'] }}" class="btn btn-primary btn-xs shrink-0" wire:navigate>Рабочее пространство</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-marycard>
    @else
        <x-empty-state
            title="Пока нет хакатонов"
            description="Найдите событие в каталоге и подайте заявку от команды."
            icon="heroicons:rocket-launch"
            :action-href="route('hackatons.index')"
            action-label="Каталог хакатонов"
            :secondary-action-href="route('teams.create')"
            secondary-action-label="Создать команду"
        />
    @endif

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('hackatons.index') }}" class="btn btn-primary">Каталог хакатонов</a>
        <a href="{{ route('teams.create') }}" class="btn btn-outline">Создать команду</a>
    </div>
</div>
