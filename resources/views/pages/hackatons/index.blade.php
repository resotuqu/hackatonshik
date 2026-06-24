@php
    $hasFilters =
        filled($q)
        || filled($start_at)
        || $sort !== 'newest'
        || $level !== 'all'
        || $statusGroup !== 'all';

    $loadingTargets = 'search,clearFilters,saveCurrentFilter,applySavedFilter,quickApplyHackaton,q,start_at,sort,level,statusGroup,nextPage,previousPage,gotoPage,setPage';

    $statusGroupOptions = [
        'all'      => 'Все',
        'upcoming' => 'Предстоящие',
        'active'   => 'Идут сейчас',
        'finished' => 'Завершены',
    ];

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

<div class="space-y-6">
    {{-- Page header --}}
    <x-page-header
        eyebrow="Каталог хакатонов"
        title="Хакатоны"
        description="Находите подходящие соревнования, подавайте заявки командой и сражайтесь за призы."
    >
        <x-slot:actions>
            <div class="flex flex-wrap gap-1" role="tablist" aria-label="Фильтр по статусу">
                @foreach ($statusGroupOptions as $value => $label)
                    <button
                        type="button"
                        wire:click="setStatusGroup('{{ $value }}')"
                        role="tab"
                        aria-selected="{{ $statusGroup === $value ? 'true' : 'false' }}"
                        class="btn btn-sm {{ $statusGroup === $value ? 'btn-primary' : 'btn-ghost border border-base-300' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <p class="t-meta font-medium tabular-nums">
                    {{ $totalHackatons }} {{ $hackatonsWord }}
                </p>
                @if ($canCreate)
                    <a href="/hackatons/create" wire:navigate class="ui-cta-primary btn-sm">
                        <x-app-icon icon="heroicons:plus-circle" class="h-4 w-4" />
                        Создать хакатон
                    </a>
                @elseif (! auth()->check())
                    <a href="{{ route('login') }}" class="ui-cta-outline btn-sm gap-1.5">
                        <x-app-icon icon="heroicons:arrow-right-on-rectangle" class="h-4 w-4" />
                        Войти
                    </a>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Filters (always visible) --}}
    <section aria-label="Фильтры">
        <div class="ui-surface-soft space-y-4 px-4 py-4 sm:px-5 sm:py-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-end">
                <label class="form-control w-full min-w-0 flex-1 lg:max-w-md">
                    <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/70">Поиск</span></span>
                    <input
                        type="search"
                        class="input input-bordered input-sm w-full border-base-300 bg-base-100 sm:input-md"
                        placeholder="Название хакатона…"
                        autocomplete="off"
                        wire:model.live.debounce.300ms="q"
                    />
                </label>

                <label class="form-control w-full min-w-0 lg:w-64">
                    <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/70">Сортировка</span></span>
                    <select class="select select-bordered select-sm w-full border-base-300 bg-base-100 sm:select-md" wire:model.live="sort">
                        <option value="newest">Сначала новые</option>
                        <option value="start_soonest">Ближайший старт</option>
                        <option value="biggest_prize">Самый большой призовой фонд</option>
                    </select>
                </label>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <label class="form-control w-full min-w-0">
                    <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/70">Уровень</span></span>
                    <select class="select select-bordered select-sm w-full border-base-300 bg-base-100 sm:select-md" wire:model.live="level">
                        <option value="all">Любой</option>
                        @foreach (\App\Enums\HackatonLevel::cases() as $levelCase)
                            <option value="{{ $levelCase->value }}">{{ $levelCase->label() }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-control w-full min-w-0">
                    <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/70">Старт от</span></span>
                    <input type="date" class="input input-bordered input-sm w-full border-base-300 bg-base-100 sm:input-md" wire:model.live="start_at" />
                </label>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="ui-cta-primary btn-sm gap-1.5" wire:click="search">
                    <x-app-icon icon="heroicons:magnifying-glass" class="h-4 w-4" />
                    Искать
                </button>
                @if ($hasFilters)
                    <button type="button" class="ui-cta-ghost btn-sm gap-1.5" wire:click="clearFilters">
                        <x-app-icon icon="heroicons:arrow-path" class="h-4 w-4" />
                        Сбросить
                    </button>
                @endif
            </div>
        </div>

        @if ($hasFilters)
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium text-base-content/50">Фильтры:</span>
                @if (filled($q))
                    <span class="badge badge-outline border-base-300 text-base-content/80">Поиск: {{ $q }}</span>
                @endif
                @if (filled($start_at))
                    <span class="badge badge-outline border-base-300 text-base-content/80">Старт от: {{ \Illuminate\Support\Carbon::parse($start_at)->format('d.m.Y') }}</span>
                @endif
                @if ($level !== 'all')
                    @php $levelEnum = \App\Enums\HackatonLevel::tryFrom($level); @endphp
                    <span class="badge badge-outline border-base-300 text-base-content/80">Уровень: {{ $levelEnum?->label() ?? $level }}</span>
                @endif
                @if ($statusGroup !== 'all')
                    <span class="badge badge-outline border-base-300 text-base-content/80">{{ $statusGroupOptions[$statusGroup] ?? $statusGroup }}</span>
                @endif
            </div>
        @endif

        @auth
            <div class="mt-3 card border border-base-300 bg-base-100">
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
                                <p class="text-sm text-base-content/70">Пока нет сохранённых фильтров.</p>
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

    <div class="relative">
        <div
            wire:loading
            wire:key="hackatons-catalog-loading"
            wire:target="{{ $loadingTargets }}"
            class="absolute inset-0 z-10 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3"
        >
            @foreach (range(1, 6) as $skeletonIndex)
                <div wire:key="hackaton-skeleton-{{ $skeletonIndex }}">
                    <x-hackaton-card-skeleton />
                </div>
            @endforeach
        </div>

        <div class="grid min-h-0 grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($this->hackatons as $hackaton)
                @php
                    $canQuickApply = auth()->check() && auth()->user()->canParticipate();
                @endphp
                <x-hackaton-card
                    wire:key="hackaton-card-{{ $hackaton->id }}"
                    class="min-h-0"
                    :hackaton="$hackaton"
                    :can-quick-apply="$canQuickApply"
                />
            @empty
                <div wire:key="hackatons-catalog-empty" class="col-span-full">
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
    </div>

    @if ($this->hackatons->isNotEmpty())
        <div class="mt-6">
            {{ $this->hackatons->links(data: ['scrollTo' => false]) }}
        </div>
    @endif
</div>
