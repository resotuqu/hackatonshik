<?php

use App\Enums\HackatonLevel;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\ListAnalyticsEvent;
use App\Models\SavedListFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

new #[Layout('layouts::app', ['title' => "Хакатоны"])]
class extends Component {
    use \Livewire\WithPagination;

    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'start_at')]
    public string $start_at = '';

    #[Url(as: 'status')]
    public string $status = 'all';

    #[Url(as: 'public_only')]
    public bool $public_only = false;

    #[Url(as: 'sort')]
    public string $sort = 'newest';

    #[Url(as: 'level')]
    public string $level = 'all';

    #[Url(as: 'with_prizes')]
    public bool $with_prizes = false;

    #[Url(as: 'preset')]
    public string $preset = 'all';

    public string $saved_filter_name = '';

    #[Computed]
    public function hackatons()
    {
        return Hackaton::query()
            ->select([
                'id', 'title', 'image_url', 'start_at', 'end_at', 'is_public', 'status',
                'prize_fund', 'prize_places_count', 'level', 'registration_deadline_at',
            ])
            ->withCount('teams')
            ->withCount(['teams as participants_count' => fn (Builder $query) => $query
                ->join('team_roles', 'team_roles.team_id', '=', 'teams.id')
                ->whereNotNull('team_roles.user_id')])
            ->when($this->q !== '', function (Builder $query): void {
                $query->where('title', 'like', '%'.$this->q.'%');
            })
            ->when($this->start_at !== '', function (Builder $query): void {
                $query->where('start_at', '>=', $this->start_at);
            })
            ->when($this->status !== 'all', fn (Builder $query): Builder => $query->where('status', $this->status))
            ->when($this->level !== 'all', fn (Builder $query): Builder => $query->where('level', $this->level))
            ->when($this->with_prizes, fn (Builder $query): Builder => $query->whereNotNull('prize_fund')->where('prize_fund', '>', 0))
            ->when($this->public_only, fn (Builder $query) => $query->where('is_public', true))
            ->when($this->preset === 'active_now', fn (Builder $query) => $query->whereIn('status', [
                HackatonStatus::REGISTRATION_OPEN->value,
                HackatonStatus::IN_PROGRESS->value,
                HackatonStatus::PUBLISHED->value,
            ]))
            ->when($this->preset === 'finished', fn (Builder $query) => $query->whereIn('status', [
                HackatonStatus::FINISHED->value,
                HackatonStatus::ARCHIVED->value,
            ]))
            ->when($this->preset === 'beginner', fn (Builder $query) => $query->where('level', HackatonLevel::Beginner->value))
            ->when($this->preset === 'with_prizes', fn (Builder $query) => $query->whereNotNull('prize_fund')->where('prize_fund', '>', 0))
            ->when($this->sort === 'start_soonest', fn (Builder $query) => $query->orderBy('start_at')->orderByDesc('id'))
            ->when($this->sort === 'newest', fn (Builder $query) => $query->orderByDesc('id'))
            ->when($this->sort === 'biggest_prize', fn (Builder $query) => $query->orderByDesc('prize_fund')->orderByDesc('id'))
            ->paginate(9);
    }

    #[Computed]
    public function savedFilters()
    {
        if (! Auth::check()) {
            return collect();
        }

        return SavedListFilter::query()
            ->where('user_id', Auth::id())
            ->where('list_key', 'hackatons')
            ->latest()
            ->limit(2)
            ->get();
    }

    public function mount(): void
    {
        $this->trackListEvent('list_view');
    }

    public function search(): void
    {
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function clearFilters(): void
    {
        $this->reset(['q', 'start_at', 'status', 'public_only', 'sort', 'level', 'with_prizes', 'preset']);
        $this->status = 'all';
        $this->level = 'all';
        $this->preset = 'all';
        $this->sort = 'newest';
        $this->public_only = false;
        $this->with_prizes = false;
        $this->resetPage();
    }

    public function setStatusChip(string $statusValue): void
    {
        $this->status = $statusValue;
        $this->preset = 'all';
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function setPreset(string $preset): void
    {
        if (! in_array($preset, ['all', 'active_now', 'finished', 'beginner', 'with_prizes'], true)) {
            return;
        }

        $this->preset = $preset;
        if ($preset !== 'all') {
            $this->status = 'all';
            $this->level = 'all';
            $this->with_prizes = false;
        }
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function quickApplyHackaton(int $hackatonId): void
    {
        if (! Auth::check()) {
            return;
        }

        $teamId = Cache::remember(
            "quick-hackaton-team-{$hackatonId}-".Auth::id(),
            now()->addMinutes(5),
            fn () => \App\Models\Team::query()
                ->where('user_id', Auth::id())
                ->where('hackaton_id', $hackatonId)
                ->value('id')
        );

        if (! $teamId) {
            session()->flash('warning', 'Нет доступной команды для быстрой заявки.');

            return;
        }

        $application = HackatonApplication::query()->firstOrNew([
            'hackaton_id' => $hackatonId,
            'team_id' => (int) $teamId,
        ]);

        $application->fill([
            'status' => \App\Enums\ApplicationStatus::PENDING,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'message' => null,
        ]);
        $application->save();

        $this->trackListEvent('quick_apply_click', ['hackaton_id' => $hackatonId, 'team_id' => (int) $teamId]);
        session()->flash('success', 'Быстрая заявка отправлена.');
    }

    public function openHackaton(int $hackatonId)
    {
        $this->trackListEvent('card_open', ['hackaton_id' => $hackatonId]);

        return redirect()->route('hackatons.show', ['hackaton' => $hackatonId]);
    }

    public function saveCurrentFilter(): void
    {
        if (! Auth::check() || trim($this->saved_filter_name) === '') {
            return;
        }

        SavedListFilter::query()->updateOrCreate(
            [
                'user_id' => Auth::id(),
                'list_key' => 'hackatons',
                'name' => trim($this->saved_filter_name),
            ],
            ['filters' => $this->currentFilters()]
        );

        $this->saved_filter_name = '';
    }

    public function applySavedFilter(int $id): void
    {
        $filter = SavedListFilter::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->where('list_key', 'hackatons')
            ->first();

        if (! $filter) {
            return;
        }

        $payload = $filter->filters ?? [];
        $this->q = (string) ($payload['q'] ?? '');
        $this->start_at = (string) ($payload['start_at'] ?? '');
        $this->status = (string) ($payload['status'] ?? 'all');
        $this->sort = (string) ($payload['sort'] ?? 'newest');
        $this->public_only = (bool) ($payload['public_only'] ?? false);
        $this->level = (string) ($payload['level'] ?? 'all');
        $this->with_prizes = (bool) ($payload['with_prizes'] ?? false);
        $this->preset = (string) ($payload['preset'] ?? 'all');
        $this->search();
    }

    private function currentFilters(): array
    {
        return [
            'q' => $this->q,
            'start_at' => $this->start_at,
            'status' => $this->status,
            'public_only' => $this->public_only,
            'sort' => $this->sort,
            'level' => $this->level,
            'with_prizes' => $this->with_prizes,
            'preset' => $this->preset,
        ];
    }

    private function trackListEvent(string $eventName, array $payload = []): void
    {
        ListAnalyticsEvent::query()->create([
            'user_id' => Auth::id(),
            'list_key' => 'hackatons',
            'event_name' => $eventName,
            'payload' => $payload,
        ]);
    }
}
?>

@php
    $hasFilters =
        filled($q)
        || filled($start_at)
        || $status !== 'all'
        || $sort !== 'newest'
        || $public_only
        || $level !== 'all'
        || $with_prizes
        || $preset !== 'all';

    $loadingTargets = 'search,clearFilters,setStatusChip,setPreset,saveCurrentFilter,applySavedFilter,quickApplyHackaton,q,start_at,status,sort,public_only,level,with_prizes,preset,nextPage,previousPage,gotoPage,setPage';

    $statusChips = [
        ['value' => 'all', 'label' => 'Любой'],
        ['value' => \App\Enums\HackatonStatus::REGISTRATION_OPEN->value, 'label' => 'Регистрация'],
        ['value' => \App\Enums\HackatonStatus::IN_PROGRESS->value, 'label' => 'Идёт'],
        ['value' => \App\Enums\HackatonStatus::JUDGING->value, 'label' => 'Судейство'],
        ['value' => \App\Enums\HackatonStatus::FINISHED->value, 'label' => 'Завершён'],
        ['value' => \App\Enums\HackatonStatus::PUBLISHED->value, 'label' => 'Анонс'],
        ['value' => \App\Enums\HackatonStatus::DRAFT->value, 'label' => 'Черновик'],
        ['value' => \App\Enums\HackatonStatus::ARCHIVED->value, 'label' => 'Архив'],
    ];

    $presetChips = [
        ['value' => 'active_now', 'label' => 'Активные сейчас', 'icon' => 'heroicons:bolt'],
        ['value' => 'finished', 'label' => 'Завершённые', 'icon' => 'heroicons:flag'],
        ['value' => 'beginner', 'label' => 'Для новичков', 'icon' => 'heroicons:sparkles'],
        ['value' => 'with_prizes', 'label' => 'С призами', 'icon' => 'heroicons:trophy'],
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

    $canCreate = auth()->check() && (auth()->user()->isOrganizer() || auth()->user()->isAdmin());
@endphp

<div class="space-y-8">
    {{-- Hero section --}}
    <section class="relative overflow-hidden rounded-3xl border border-base-300 bg-base-200/50 px-5 py-8 sm:px-8 sm:py-10">
        <div class="pointer-events-none absolute inset-0 opacity-60" aria-hidden="true">
            <div class="absolute -top-24 -right-16 h-64 w-64 rounded-full bg-primary/30 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-secondary/25 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/3 h-40 w-40 -translate-y-1/2 rounded-full bg-accent/20 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="min-w-0 space-y-3">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary/30 bg-primary/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-primary">
                    <x-app-icon icon="heroicons:rocket-launch" class="h-3.5 w-3.5" />
                    Каталог хакатонов
                </div>
                <h1 class="font-display text-3xl font-black tracking-tight text-base-content sm:text-4xl lg:text-5xl">
                    <span class="bg-linear-to-r from-primary via-accent to-secondary bg-clip-text text-transparent">
                        Хакатоны
                    </span>
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
                    <a href="/hackatons/create" wire:navigate class="btn btn-primary btn-md gap-2 shadow-lg shadow-primary/20">
                        <x-app-icon icon="heroicons:plus-circle" class="h-5 w-5" />
                        Создать хакатон
                    </a>
                @elseif (! auth()->check())
                    <a href="{{ route('login') }}" class="btn btn-outline btn-md gap-2">
                        <x-app-icon icon="heroicons:arrow-right-on-rectangle" class="h-5 w-5" />
                        Войти, чтобы участвовать
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Preset chips --}}
    <section aria-label="Быстрые подборки" class="space-y-2">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-bold uppercase tracking-widest text-base-content/55">Подборки</span>
            @if ($preset !== 'all')
                <button type="button" class="btn btn-ghost btn-xs gap-1" wire:click="setPreset('all')">
                    <x-app-icon icon="heroicons:x-mark" class="h-3.5 w-3.5" />
                    Сбросить подборку
                </button>
            @endif
        </div>
        <div class="-mx-1 flex snap-x snap-mandatory gap-2 overflow-x-auto px-1 pb-1">
            @foreach ($presetChips as $chip)
                @php $isActive = $preset === $chip['value']; @endphp
                <button
                    type="button"
                    wire:click="setPreset('{{ $chip['value'] }}')"
                    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                    @class([
                        'btn btn-sm shrink-0 snap-start gap-1.5 transition-all duration-200',
                        'btn-primary shadow-md shadow-primary/20' => $isActive,
                        'btn-ghost border border-base-300 bg-base-100 hover:border-primary/40' => ! $isActive,
                    ])
                >
                    <x-app-icon icon="{{ $chip['icon'] }}" class="h-4 w-4" />
                    {{ $chip['label'] }}
                </button>
            @endforeach
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

                {{-- Status chips --}}
                <div class="flex flex-col gap-2">
                    <span class="text-xs font-medium uppercase tracking-wide text-base-content/60">Статус</span>
                    <div class="-mx-1 flex snap-x snap-mandatory gap-2 overflow-x-auto px-1 pb-1" role="group" aria-label="Фильтр по статусу">
                        @foreach ($statusChips as $chip)
                            @php $pressed = $status === $chip['value']; @endphp
                            <button
                                type="button"
                                class="btn btn-sm shrink-0 snap-start border-base-300 {{ $pressed ? 'btn-primary' : 'btn-ghost bg-base-100' }}"
                                wire:click="setStatusChip('{{ $chip['value'] }}')"
                                aria-pressed="{{ $pressed ? 'true' : 'false' }}"
                            >
                                {{ $chip['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Level + extra filters --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
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
                    <label class="label cursor-pointer justify-start gap-3 rounded-xl border border-base-300 bg-base-100 px-3 py-2">
                        <input type="checkbox" class="checkbox checkbox-primary checkbox-sm" wire:model.live="with_prizes" />
                        <span class="label-text text-sm leading-snug">Только с призами</span>
                    </label>
                    <label class="label cursor-pointer justify-start gap-3 rounded-xl border border-base-300 bg-base-100 px-3 py-2">
                        <input type="checkbox" class="checkbox checkbox-primary checkbox-sm" wire:model.live="public_only" />
                        <span class="label-text text-sm leading-snug">Только публичные</span>
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
                            <span class="badge badge-primary badge-outline">Старт от: {{ Carbon::parse($start_at)->format('d.m.Y H:i') }}</span>
                        @endif
                        @if ($status !== 'all')
                            @php $statusLabel = collect($statusChips)->firstWhere('value', $status)['label'] ?? $status; @endphp
                            <span class="badge badge-primary badge-outline">Статус: {{ $statusLabel }}</span>
                        @endif
                        @if ($level !== 'all')
                            @php $levelEnum = \App\Enums\HackatonLevel::tryFrom($level); @endphp
                            <span class="badge badge-primary badge-outline">Уровень: {{ $levelEnum?->label() ?? $level }}</span>
                        @endif
                        @if ($with_prizes)
                            <span class="badge badge-primary badge-outline">С призами</span>
                        @endif
                        @if ($public_only)
                            <span class="badge badge-primary badge-outline">Только публичные</span>
                        @endif
                        @if ($preset !== 'all')
                            @php $presetLabel = collect($presetChips)->firstWhere('value', $preset)['label'] ?? $preset; @endphp
                            <span class="badge badge-secondary badge-outline">Подборка: {{ $presetLabel }}</span>
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
                    $canQuickApply = auth()->check() && ! auth()->user()->isOrganizer();
                @endphp
                <div
                    wire:key="hackaton-card-{{ $hackaton->id }}"
                    class="motion-safe:animate-card-enter"
                    style="animation-delay: {{ ($loop->index % 9) * 40 }}ms;"
                >
                    <x-hackaton-card
                        :hackaton="$hackaton"
                        :can-quick-apply="$canQuickApply"
                    />
                </div>
            @empty
                <div class="col-span-full">
                    <div class="mx-auto flex max-w-lg flex-col items-center gap-6 rounded-3xl border border-base-300 bg-base-100 px-6 py-12 text-center shadow-sm">
                        <div class="text-primary" aria-hidden="true">
                            <svg class="h-40 w-full max-w-56" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="hg" x1="0" y1="0" x2="200" y2="160" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="currentColor" stop-opacity="0.35" />
                                        <stop offset="1" stop-color="currentColor" stop-opacity="0.05" />
                                    </linearGradient>
                                </defs>
                                <rect width="200" height="160" rx="16" fill="url(#hg)" />
                                <path d="M20 40h160M20 80h160M20 120h160" stroke="currentColor" stroke-opacity="0.2" stroke-width="1" />
                                <path d="M40 20v120M80 20v120M120 20v120M160 20v120" stroke="currentColor" stroke-opacity="0.15" stroke-width="1" />
                                <circle cx="100" cy="78" r="28" stroke="currentColor" stroke-opacity="0.5" stroke-width="2" fill="none" />
                                <path d="M88 78l8 8 16-16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <h2 class="font-display text-xl font-semibold text-base-content">Хакатоны не найдены</h2>
                            <p class="text-sm leading-relaxed text-base-content/70">
                                Попробуйте изменить параметры поиска или выбрать другую подборку.
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <button type="button" class="btn btn-primary" wire:click="setPreset('active_now')">
                                Показать активные
                            </button>
                            <button type="button" class="btn btn-outline" wire:click="clearFilters">
                                Сбросить фильтры
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($this->hackatons->isNotEmpty())
            <div class="mt-6">{{ $this->hackatons->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </div>
</div>

