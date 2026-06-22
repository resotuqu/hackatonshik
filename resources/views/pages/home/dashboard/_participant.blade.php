@php
    $isFull = ($context ?? 'home') === 'full';
    $hackatons = $isFull ? $participantHackatons : $participantHackatonsPreview;
    $hackatonsTitle = $isFull ? 'Хакатоны' : 'Предстоящие хакатоны';
    $hackatonsSubtitle = $isFull ? null : 'Ваши и ближайшие публичные';
@endphp

<div id="participant-hackaton-dashboard" class="scroll-mt-24 space-y-8">
    @if (! $isFull)
        <x-dashboard.role-header
            icon="heroicons:user"
            title="Участник"
            subtitle="Ваши команды, заявки и хакатоны"
            :panel-href="route('participant.hackatons')"
            panel-label="Подробнее"
        />
    @endif

    @if (! $isFull)
        <x-recommended-teams :recommendations="$recommendedTeams ?? []" />
    @endif

    @if ($participantNextStepTitle !== '')
        <x-dashboard.next-step
            :title="$participantNextStepTitle"
            :hint="$participantNextStepHint"
            :href="$participantNextStepHref"
            :action-label="$participantNextStepLabel"
        />
    @endif

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <x-dashboard.stat-card
            label="Команды"
            :value="$teamsCount"
            icon="heroicons:user-group"
            :href="route('profile.teams')"
        />
        <x-dashboard.stat-card
            label="Сертификаты"
            :value="$certificatesCount"
            icon="heroicons:academic-cap"
            :href="route('profile.certificates')"
        />
        <x-dashboard.stat-card
            label="Заявки в команды"
            :value="$pendingTeamApplicationsCount"
            icon="heroicons:envelope"
            :href="route('profile.teams').'#pending-team-role-applications'"
            link-text="Просмотр →"
        />
        <x-dashboard.stat-card
            label="Заявки на хакатоны"
            :value="$pendingHackatonApplicationsCount"
            icon="heroicons:rocket-launch"
            :href="route('hackatons.index')"
            link-text="Каталог →"
        />
    </div>

    @if (count($hackatonApplicationsPreview) > 0)
        <div class="ui-surface-card overflow-hidden">
            <div class="border-b border-base-300 px-5 py-3.5">
                <h3 class="text-sm font-semibold">Заявки команд на хакатоны</h3>
            </div>
            <ul>
                @foreach ($hackatonApplicationsPreview as $row)
                    <li class="flex flex-wrap items-center justify-between gap-3 border-b border-base-300 px-5 py-3 last:border-0">
                        <div class="min-w-0">
                            <a href="{{ route('hackatons.show', $row['hackaton_id']) }}#participant-hackaton-applications" class="text-sm font-medium transition-colors hover:text-primary" wire:navigate>{{ $row['title'] }}</a>
                            <span class="text-sm text-base-content/60"> — {{ $row['team_title'] }}</span>
                        </div>
                        <span class="badge badge-warning badge-sm shrink-0">{{ $row['status_label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (count($hackatons) > 0)
        <div class="ui-surface-card overflow-hidden">
            <div class="flex items-center justify-between border-b border-base-300 px-5 py-3.5">
                <div>
                    <h3 class="text-sm font-semibold">{{ $hackatonsTitle }}</h3>
                    @if ($hackatonsSubtitle)
                        <p class="text-xs text-base-content/50">{{ $hackatonsSubtitle }}</p>
                    @endif
                </div>
                <a href="{{ route('hackatons.index') }}" class="btn btn-ghost btn-xs" wire:navigate>Все</a>
            </div>
            <ul>
                @foreach ($hackatons as $row)
                    <li class="flex flex-wrap items-center justify-between gap-3 border-b border-base-300 px-5 py-3 last:border-0">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('hackatons.show', $row['id']) }}" class="text-sm font-medium transition-colors hover:text-primary" wire:navigate>{{ $row['title'] }}</a>
                            @if ($row['start_at'])
                                <div class="mt-0.5 flex items-center gap-1 text-xs text-base-content/50">
                                    <x-app-icon icon="heroicons:calendar" class="h-3 w-3" />
                                    {{ $row['start_at'] }}
                                </div>
                            @endif
                        </div>
                        @if ($isFull && ! empty($row['hub_url']))
                            <a href="{{ $row['hub_url'] }}" class="btn btn-primary btn-xs shrink-0" wire:navigate>Рабочее пространство</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @elseif ($isFull)
        <x-empty-state
            embedded
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
        @if (! $isFull)
            <a href="{{ route('participant.hackatons') }}" class="ui-cta-primary" wire:navigate>Мои заявки и хакатоны</a>
        @endif
        <a href="{{ route('hackatons.index') }}" class="{{ $isFull ? 'ui-cta-primary' : 'ui-cta-outline' }}" wire:navigate>Каталог хакатонов</a>
        @if (! $isFull)
            <a href="{{ route('teams.create') }}" class="ui-cta-outline" wire:navigate>Создать команду</a>
            <a href="{{ route('profile.teams') }}" class="ui-cta-outline" wire:navigate>Мои команды</a>
        @else
            <a href="{{ route('teams.create') }}" class="ui-cta-outline" wire:navigate>Создать команду</a>
        @endif
    </div>
</div>
