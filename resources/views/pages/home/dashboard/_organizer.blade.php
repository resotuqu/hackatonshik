@php
    $isFull = ($context ?? 'home') === 'full';

    if ($isFull) {
        $hackatonsCount = $summary['hackatonsTotal'] ?? 0;
        $pendingHackatonApplicationsCount = $summary['pendingApplications'] ?? 0;
        $organizerFirstPendingHackatonId = isset($hackatons)
            ? $hackatons->first(fn ($hackaton) => ($hackaton->pending_applications_count ?? 0) > 0)?->id
            : null;
    }

    $pendingReviewHref = $pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId
        ? route('hackatons.show', $organizerFirstPendingHackatonId).'?applications_status=pending#hackaton-tab-participants'
        : ($isFull && $pendingHackatonApplicationsCount > 0 ? route('organizer.applications') : null);
@endphp

<section class="space-y-6" data-test="{{ $isFull ? 'organizer-dashboard-summary' : 'home-organizer-dashboard' }}">
    @if ($isFull)
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="ui-heading-display text-2xl font-bold sm:text-3xl">Организатор</h1>
                <p class="mt-1 text-sm text-base-content/70">Управление хакатонами, заявками и полный список событий.</p>
            </div>
            <a href="{{ route('hackatons.create') }}" class="ui-cta-primary btn-sm shrink-0 self-start sm:self-auto" wire:navigate>
                Создать хакатон
            </a>
        </header>
    @else
        <x-dashboard.role-header
            icon="heroicons:building-office"
            title="Организатор"
            subtitle="Управление хакатонами"
            icon-tone="secondary"
            :panel-href="route('organizer.dashboard')"
        />
    @endif

    <div @class(['grid grid-cols-1 gap-3', $isFull ? 'sm:grid-cols-2 xl:grid-cols-4' : 'sm:grid-cols-2'])>
        <x-dashboard.stat-card
            :label="$isFull ? 'Активные хакатоны' : 'Мои хакатоны'"
            :value="$isFull ? ($summary['activeHackatons'] ?? 0) : $hackatonsCount"
            icon="heroicons:trophy"
            :href="$isFull ? null : route('organizer.dashboard')"
            :link-text="$isFull ? '' : 'Управлять →'"
        />

        <x-dashboard.stat-card
            label="Заявок ожидают решения"
            :value="$pendingHackatonApplicationsCount"
            icon="heroicons:inbox"
            :highlight="$pendingHackatonApplicationsCount > 0"
            :href="$pendingReviewHref"
            :link-text="$pendingReviewHref ? ($organizerFirstPendingHackatonId ? 'Рассмотреть →' : 'Все заявки →') : ''"
        >
            @if ($pendingReviewHref === null)
                <p class="text-xs text-base-content/50">По всем вашим хакатонам</p>
            @endif
        </x-dashboard.stat-card>

        @if ($isFull)
            <x-dashboard.stat-card
                label="Участники (роли)"
                :value="$summary['participantsTotal'] ?? 0"
                icon="heroicons:user-group"
            />
            <x-dashboard.stat-card
                label="Всего создано"
                :value="$summary['hackatonsTotal'] ?? 0"
                icon="heroicons:archive-box"
            />
        @endif
    </div>

    @if (! $isFull)
        <div class="flex flex-wrap gap-3">
            @if ($pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId)
                <a href="{{ route('hackatons.show', $organizerFirstPendingHackatonId) }}?applications_status=pending#hackaton-tab-participants" class="ui-cta-primary" wire:navigate>Рассмотреть заявки</a>
            @endif
            <a href="{{ route('organizer.dashboard') }}" @class([
                $pendingHackatonApplicationsCount > 0 && $organizerFirstPendingHackatonId ? 'ui-cta-outline' : 'ui-cta-primary',
            ]) wire:navigate>Мои хакатоны</a>
            <a href="{{ route('hackatons.create') }}" class="ui-cta-outline" wire:navigate>Создать хакатон</a>
            <a href="{{ route('hackatons.index') }}" class="ui-cta-outline" wire:navigate>Каталог хакатонов</a>
        </div>
    @endif
</section>
