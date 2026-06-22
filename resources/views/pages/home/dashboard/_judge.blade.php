<section class="space-y-6" data-test="home-judge-dashboard">
    <x-dashboard.role-header
        icon="heroicons:scale"
        title="Судья"
        subtitle="Оценка проектов и экспертиза"
        icon-tone="accent"
        :panel-href="$judgeHackatonsCount > 0 ? route('judge.dashboard') : null"
    />

    @if ($judgeHackatonsCount === 0)
        <x-empty-state
            embedded
            title="Пока нет назначенных хакатонов"
            description="Когда организатор добавит вас в состав жюри, события появятся здесь."
            icon="heroicons:scale"
            test-id="judge-dashboard-empty"
            :action-href="route('hackatons.index')"
            action-label="Каталог хакатонов"
        />
    @else
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <x-dashboard.stat-card
                label="Назначено хакатонов"
                :value="$judgeHackatonsCount"
                icon="heroicons:briefcase"
                :href="route('judge.dashboard')"
                link-text="Панель судьи →"
            />
        </div>

        @if (count($judgeHackatonsPreview) > 0)
            <div class="ui-surface-card overflow-hidden">
                <div class="flex items-center justify-between border-b border-base-300 px-5 py-3.5">
                    <h3 class="text-sm font-semibold">Ближайшие события</h3>
                </div>
                <ul>
                    @foreach ($judgeHackatonsPreview as $row)
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
                            <a href="{{ route('judge.hackatons.show', $row['id']) }}" class="btn btn-primary btn-xs shrink-0" wire:navigate>К оценке</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('judge.dashboard') }}" class="ui-cta-primary" wire:navigate>Панель судьи</a>
        <a href="{{ route('hackatons.index') }}" class="ui-cta-outline" wire:navigate>Каталог хакатонов</a>
    </div>
</section>
