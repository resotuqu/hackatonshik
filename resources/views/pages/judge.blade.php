@php
    $loadingTargets = 'nextPage,previousPage,gotoPage,setPage';
@endphp

<div class="space-y-8">
    <section class="ui-page-hero">
        <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="min-w-0 space-y-3">
                <p class="text-sm text-base-content/60">Панель судьи</p>
                <h1 class="ui-heading-display text-3xl font-bold sm:text-4xl lg:text-5xl">
                    Ваша экспертиза
                </h1>
                <p class="max-w-2xl text-base text-base-content/70">Оценивайте проекты участников и помогайте определять победителей.</p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-3">
                <a href="/hackatons" class="ui-cta-primary">
                    <x-app-icon icon="heroicons:trophy" class="h-5 w-5" />
                    Каталог событий
                </a>
            </div>
        </div>
    </section>

    @if ($judgeHackatonsCount === 0)
        <x-empty-state
            title="Нет назначенных хакатонов"
            description="Когда организатор добавит вас в состав жюри, события появятся в этой панели."
            icon="heroicons:user-group"
            action-href="/hackatons"
            action-label="Посмотреть хакатоны"
        />
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="ui-surface-card h-full p-6">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-base-content/60">Всего хакатонов</p>
                            <p class="ui-heading-display text-4xl font-semibold tabular-nums">{{ $judgeHackatonsCount }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <x-app-icon icon="heroicons:briefcase" class="h-6 w-6" />
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="/hackatons" class="btn btn-ghost btn-sm btn-block justify-between border-base-300">
                            Все события
                            <x-app-icon icon="heroicons:chevron-right" class="h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="ui-surface-card h-full overflow-hidden">
                    <div class="border-b border-base-300 bg-base-200 px-6 py-4">
                        <h2 class="font-display text-lg font-semibold">Ближайшие события</h2>
                    </div>
                    <div class="divide-y divide-base-300">
                        @foreach ($judgeHackatonsPreview as $row)
                            <div class="group flex items-center justify-between gap-4 p-4 transition-colors hover:bg-base-200/50">
                                <div class="min-w-0 flex-1 space-y-1">
                                    <a href="{{ route('hackatons.show', $row['id']) }}" class="ui-heading-display block truncate font-semibold hover:text-primary transition-colors">
                                        {{ $row['title'] }}
                                    </a>
                                    @if ($row['start_at'])
                                        <div class="flex items-center gap-1.5 text-xs text-base-content/60">
                                            <x-app-icon icon="heroicons:calendar" class="h-3.5 w-3.5" />
                                            {{ $row['start_at'] }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex shrink-0 gap-2">
                                    <a href="{{ route('hackatons.show', $row['id']) }}#hackaton-cases" class="btn btn-primary btn-sm">
                                        К кейсам
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-between rounded-lg border border-base-300 bg-base-100 p-4">
        <p class="text-sm text-base-content/70">Нужна помощь в оценке?</p>
        <a href="/profile" class="ui-cta-outline btn-sm">
            <x-app-icon icon="heroicons:user-circle" class="h-4 w-4" />
            Профиль
        </a>
    </div>
</div>
