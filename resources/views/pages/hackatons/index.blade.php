@php
    $hasFilters =
        filled($q)
        || filled($start_at)
        || $sort !== 'newest'
        || $level !== 'all';

    $loadingTargets = 'search,clearFilters,saveCurrentFilter,applySavedFilter,quickApplyHackaton,q,start_at,sort,level,nextPage,previousPage,gotoPage,setPage';

    $totalHackatons = $this->hackatons->total();
    $hc = $totalHackatons % 100;
    $hn = $totalHackatons % 10;
    $hackatonsWord = match (true) {
        $hc >= 11 && $hc <= 19 => 'хакатонов',
        $hn === 1 => 'хакатон',
        $hn >= 2 && $hn <= 4 => 'хакатона',
        default => 'хакатонов',
    };

    $canCreate = auth()->check() && auth()->user()->isOrganizer();
@endphp

<div class="space-y-8">
    {{-- Hero section --}}
    <section class="ui-page-hero">
        <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="min-w-0 space-y-3">
                <p class="text-sm text-base-content/60">Каталог хакатонов</p>
                <h1 class="ui-heading-display text-3xl font-bold sm:text-4xl lg:text-5xl">
                    Хакатоны
                </h1>
                <p class="max-w-2xl text-base text-base-content/70">
                    Находите подходящие соревнования, подавайте заявки командой и сражайтесь за призы.
                </p>
                <p class="text-sm font-medium tabular-nums text-base-content/60">
                    Найдено {{ $totalHackatons }} {{ $hackatonsWord }}
                </p>
            </div>

            <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
                @if ($canCreate)
                    <a href="/hackatons/create" wire:navigate class="ui-cta-primary btn-md">
                        <x-app-icon icon="heroicons:plus-circle" class="h-5 w-5" />
                        Создать хакатон
                    </a>
                @elseif (! auth()->check())
                    <a href="{{ route('login') }}" class="ui-cta-outline btn-md gap-2">
                        <x-app-icon icon="heroicons:arrow-right-on-rectangle" class="h-5 w-5" />
                        Войти, чтобы участвовать
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Filters panel (collapsible on mobile) --}}
    <section class="space-y-4" aria-label="Фильтры">
        <div
            x-data="{ open: window.matchMedia('(min-width: 1024px)').matches }"
            x-init="window.addEventListener('resize', () => { if (window.matchMedia('(min-width: 1024px)').matches) open = true; })"
            class="rounded-2xl border border-base-300 bg-base-200/30"
        >
            <button
                type="button"
                @click="open = !open"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="hackatons-filters-panel"
                class="flex w-full cursor-pointer items-center justify-between gap-3 rounded-2xl px-4 py-3 text-left text-sm font-semibold sm:px-5 lg:cursor-default lg:pointer-events-none"
            >
                <span class="inline-flex items-center gap-2">
                    <x-app-icon icon="heroicons:adjustments-horizontal" class="h-4 w-4 text-primary" />
                    Фильтры
                    @if ($hasFilters)
                        <span class="badge badge-primary badge-xs">активны</span>
                    @endif
                </span>
                <span class="inline-flex items-center gap-2 text-xs font-medium text-base-content/60 lg:hidden">
                    <span x-show="!open">Развернуть</span>
                    <span x-show="open" x-cloak>Свернуть</span>
                    <x-app-icon icon="heroicons:chevron-down" class="h-4 w-4 transition-transform" x-bind:class="open && 'rotate-180'" />
                </span>
            </button>

            <div
                id="hackatons-filters-panel"
                x-show="open"
                x-cloak
                class="space-y-5 border-t border-base-300/70 px-4 py-4 sm:px-5 sm:py-5"
            >
                <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-end">
                    <label class="form-control w-full min-w-0 flex-1 lg:max-w-md">
                        <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Поиск</span></span>
                        <input
                            type="search"
                            class="input input-bordered input-sm w-full border-base-300 bg-base-100 sm:input-md"
                            placeholder="Название хакатона…"
                            autocomplete="off"
                            wire:model.live.debounce.300ms="q"
                        />
                    </label>

                    <label class="form-control w-full min-w-0 lg:w-64">
                        <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Сортировка</span></span>
                        <select class="select select-bordered select-sm w-full border-base-300 bg-base-100 sm:select-md" wire:model.live="sort">
                            <option value="newest">Сначала новые</option>
                            <option value="start_soonest">Ближайший старт</option>
                            <option value="biggest_prize">Самый большой призовой фонд</option>
                        </select>
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <label class="form-control w-full min-w-0">
                        <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Уровень</span></span>
                        <select class="select select-bordered select-sm w-full border-base-300 bg-base-100 sm:select-md" wire:model.live="level">
                            <option value="all">Любой</option>
                            @foreach (\App\Enums\HackatonLevel::cases() as $levelCase)
                                <option value="{{ $levelCase->value }}">{{ $levelCase->label() }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-control w-full min-w-0">
                        <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Старт от</span></span>
                        <input type="datetime-local" class="input input-bordered input-sm w-full border-base-300 bg-base-100 sm:input-md" wire:model.live="start_at" />
                    </label>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <button type="button" class="btn btn-primary btn-sm gap-1.5" wire:click="search">
                        <x-app-icon icon="heroicons:magnifying-glass" class="h-4 w-4" />
                        Искать
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm gap-1.5" wire:click="clearFilters">
                        <x-app-icon icon="heroicons:arrow-path" class="h-4 w-4" />
                        Сбросить
                    </button>
                </div>
            </div>
        </div>

        @if ($hasFilters)
            <div class="card card-border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    <p class="text-sm font-medium">Активные фильтры</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if (filled($q))
                            <span class="badge badge-primary badge-outline">Поиск: {{ $q }}</span>
                        @endif
                        @if (filled($start_at))
                            <span class="badge badge-primary badge-outline">Старт от: {{ \Illuminate\Support\Carbon::parse($start_at)->format('d.m.Y H:i') }}</span>
                        @endif
                        @if ($level !== 'all')
                            @php $levelEnum = \App\Enums\HackatonLevel::tryFrom($level); @endphp
                            <span class="badge badge-primary badge-outline">Уровень: {{ $levelEnum?->label() ?? $level }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @auth
            <div class="card card-border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body gap-3 p-4 sm:flex-row sm:flex-wrap sm:items-end">
                    <div class="min-w-0 flex-1 space-y-2">
                        <p class="text-sm font-medium">Сохранённые фильтры</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($this->savedFilters as $savedFilter)
                                <button
                                    type="button"
                                    class="btn btn-xs btn-outline"
                                    wire:click="applySavedFilter({{ $savedFilter->id }})"
                                >
                                    {{ $savedFilter->name }}
                                </button>
                            @empty
                                <p class="text-sm text-base-content/60">Пока нет сохранённых фильтров.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="flex w-full flex-col gap-2 sm:w-auto sm:min-w-[16rem] sm:flex-row">
                        <input
                            type="text"
                            class="input input-bordered input-sm w-full sm:flex-1"
                            placeholder="Название набора"
                            wire:model="saved_filter_name"
                        />
                        <button type="button" class="btn btn-primary btn-sm shrink-0" wire:click="saveCurrentFilter">Сохранить</button>
                    </div>
                </div>
            </div>
        @endauth
    </section>

    {{-- Loading state --}}
    <div wire:loading wire:target="{{ $loadingTargets }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach (range(1, 6) as $_)
            <x-hackaton-card-skeleton />
        @endforeach
    </div>

    {{-- Results --}}
    <div wire:loading.remove wire:target="{{ $loadingTargets }}">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($this->hackatons as $hackaton)
                @php
                    $canQuickApply = auth()->check() && auth()->user()->canParticipate();
                @endphp
                <div wire:key="hackaton-card-{{ $hackaton->id }}">
                    <x-hackaton-card
                        :hackaton="$hackaton"
                        :can-quick-apply="$canQuickApply"
                    />
                </div>
            @empty
                <div class="col-span-full">
                    <x-empty-state
                        :title="__('ui.hackatons.empty_title')"
                        :description="__('ui.hackatons.empty_description')"
                        icon="heroicons:rocket-launch"
                        :actionHref="'javascript:void(0)'"
                        :actionLabel="__('ui.hackatons.reset_filters')"
                        @click="$wire.clearFilters()"
                    />
                </div>
            @endforelse
        </div>

        @if ($this->hackatons->isNotEmpty())
            <div class="mt-6">{{ $this->hackatons->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </div>
</div>
