<?php

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\ListAnalyticsEvent;
use App\Models\Role;
use App\Models\SavedListFilter;
use App\Models\Skill;
use App\Models\Team;
use App\Models\TeamApplication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts::app', [
    'title' => 'Команды участников — Хакатонщик',
    'meta_description' => 'Публичные команды для участия в хакатонах: находите команду мечты или собирайте свою.',
    'canonical_url' => '/teams',
])]
class extends Component {
    use \Livewire\WithPagination;

    #[Url(as: 'tab')]
    public string $catalog_tab = 'open';

    #[Url(as: 'open_only')]
    public bool $only_open_roles = true;

    #[Url(as: 'roles')]
    public array $role_ids = [];

    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'hackaton_id')]
    public string $hackaton_id = '0';

    #[Url(as: 'skills')]
    public array $skills = [];

    #[Url(as: 'start_from')]
    public string $start_from = '';

    #[Url(as: 'sort')]
    public string $sort = 'newest';

    #[Url(as: 'team_size')]
    public string $team_size = 'all';

    public string $saved_filter_name = '';

    private function needsOpenRolesFilter(): bool
    {
        return $this->catalog_tab === 'open'
            || ($this->catalog_tab === 'all' && $this->only_open_roles);
    }

    /** @return list<int> */
    private function normalizedRoleIds(): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $this->role_ids))));
    }

    #[Computed]
    public function hackatons()
    {
        return Cache::remember('teams-filter-hackatons', now()->addMinutes(10), function () {
            $hackatons = [['id' => '0', 'name' => 'Любой']];
            foreach (Hackaton::query()->where('is_public', true)->orderBy('title')->get(['id', 'title']) as $hackaton) {
                $hackatons[] = [
                    'id' => (string) $hackaton->id,
                    'name' => $hackaton->title,
                ];
            }

            return $hackatons;
        });
    }

    #[Computed]
    public function teams()
    {
        $roleIdInts = $this->normalizedRoleIds();
        $needsOpen = $this->needsOpenRolesFilter();

        return Team::query()
            ->select(['id', 'user_id', 'title', 'description', 'image_url', 'cover_image', 'hackaton_id'])
            ->where('teams.is_public', true)
            ->with([
                'user:id,nickname',
                'hackaton:id,title,start_at,end_at',
                'roles' => fn ($q) => $q
                    ->select(['id', 'team_id', 'role_id', 'user_id'])
                    ->with([
                        'role:id,name',
                        'skills:id,name',
                        'user:id,fio,nickname,avatar_path',
                    ]),
            ])
            ->withCount('roles')
            ->withCount(['roles as empty_roles_count' => fn ($query) => $query->whereNull('user_id')])
            ->when($needsOpen, fn ($q) => $q->whereHas('roles', fn ($r) => $r->whereNull('user_id')))
            ->when($this->q !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('title', 'like', '%'.$this->q.'%')
                        ->orWhere('description', 'like', '%'.$this->q.'%')
                        ->orWhereHas('roles.skills', fn ($skillsQuery) => $skillsQuery->where('skills.name', 'like', '%'.$this->q.'%'));
                });
            })
            ->when($this->hackaton_id !== '0', function ($query) {
                $query->where('teams.hackaton_id', '=', $this->hackaton_id);
            })
            ->when($roleIdInts !== [], fn ($query) => $query->whereHas(
                'roles',
                fn ($q) => $q->whereNull('user_id')->whereIn('role_id', $roleIdInts)
            ))
            ->when(! empty($this->skills), function ($query) {
                $query->whereHas('roles.skills', function ($q) {
                    $q->whereIn('skills.id', $this->skills);
                });
            })
            ->when($this->team_size !== 'all', function ($query) {
                $query->has('roles', '>=', (int) $this->team_size);
            })
            ->when($this->start_from !== '', function ($query) {
                $query->whereHas('hackaton', function ($q) {
                    $q->where('start_at', '>=', $this->start_from);
                });
            })
            ->when($this->sort === 'start_soonest', fn ($query) => $query->join('hackatons', 'hackatons.id', '=', 'teams.hackaton_id')->orderBy('hackatons.start_at')->select('teams.*'))
            ->when($this->sort === 'newest', fn ($query) => $query->orderByDesc('id'))
            ->paginate(9);
    }

    #[Computed]
    public function skillsData()
    {
        return Cache::remember('teams-filter-skills', now()->addMinutes(10), fn () => Skill::query()->orderBy('name')->get());
    }

    #[Computed]
    public function rolesData()
    {
        return Cache::remember('teams-filter-roles', now()->addMinutes(10), function () {
            $roles = [['id' => '0', 'name' => 'Любая']];
            foreach (Role::query()->orderBy('name')->get(['id', 'name']) as $role) {
                $roles[] = [
                    'id' => (string) $role->id,
                    'name' => $role->name,
                ];
            }

            return $roles;
        });
    }

    #[Computed]
    public function rolesForChips()
    {
        return Cache::remember('teams-filter-roles-chips', now()->addMinutes(10), fn () => Role::query()->orderBy('name')->get(['id', 'name']));
    }

    #[Computed]
    public function savedFilters()
    {
        if (! Auth::check()) {
            return collect();
        }

        return SavedListFilter::query()
            ->where('user_id', Auth::id())
            ->where('list_key', 'teams')
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
        $this->reset(['q', 'hackaton_id', 'skills', 'start_from', 'sort', 'team_size', 'role_ids', 'saved_filter_name']);
        $this->hackaton_id = '0';
        $this->sort = 'newest';
        $this->team_size = 'all';
        $this->catalog_tab = 'open';
        $this->only_open_roles = true;
        $this->role_ids = [];
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function setCatalogTab(string $tab): void
    {
        if (! in_array($tab, ['open', 'all'], true)) {
            return;
        }
        $this->catalog_tab = $tab;
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function toggleRoleId(int $roleId): void
    {
        $ids = $this->normalizedRoleIds();
        if (in_array($roleId, $ids, true)) {
            $this->role_ids = array_values(array_diff($ids, [$roleId]));
        } else {
            $this->role_ids = array_values([...$ids, $roleId]);
        }
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function updatedSkills(): void
    {
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function updatedHackatonId(): void
    {
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function updatedStartFrom(): void
    {
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function updatedSort(): void
    {
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function updatedTeamSize(): void
    {
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function updatedOnlyOpenRoles(): void
    {
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function quickApplyTeam(int $teamId): void
    {
        if (! Auth::check()) {
            return;
        }

        if (Auth::user()?->isOrganizer()) {
            session()->flash('warning', 'Организатор не может подавать заявки в команды.');

            return;
        }

        $team = Team::query()->with('roles')->find($teamId);
        if (! $team || $team->user_id === Auth::id()) {
            return;
        }

        $role = $team->roles->firstWhere('user_id', null);
        if (! $role) {
            return;
        }

        $application = TeamApplication::query()->firstOrNew([
            'user_id' => Auth::id(),
            'team_role_id' => $role->id,
        ]);
        $application->fill([
            'status' => ApplicationStatus::PENDING,
            'message' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);
        $application->save();

        $this->trackListEvent('quick_apply_click', ['team_id' => $teamId, 'team_role_id' => $role->id]);
        session()->flash('success', 'Быстрый отклик отправлен.');
    }

    public function openTeam(int $teamId)
    {
        $this->trackListEvent('card_open', ['team_id' => $teamId]);

        return redirect()->route('teams.show', ['team' => $teamId]);
    }

    public function saveCurrentFilter(): void
    {
        if (! Auth::check() || trim($this->saved_filter_name) === '') {
            return;
        }

        SavedListFilter::query()->updateOrCreate(
            [
                'user_id' => Auth::id(),
                'list_key' => 'teams',
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
            ->where('list_key', 'teams')
            ->first();

        if (! $filter) {
            return;
        }

        $payload = $filter->filters ?? [];
        $this->q = (string) ($payload['q'] ?? '');
        $this->hackaton_id = (string) ($payload['hackaton_id'] ?? '0');
        $this->skills = (array) ($payload['skills'] ?? []);
        $this->start_from = (string) ($payload['start_from'] ?? '');
        $this->sort = (string) ($payload['sort'] ?? 'newest');
        $this->team_size = (string) ($payload['team_size'] ?? 'all');
        $this->catalog_tab = (string) ($payload['catalog_tab'] ?? 'open');
        if (! in_array($this->catalog_tab, ['open', 'all'], true)) {
            $this->catalog_tab = 'open';
        }
        $this->only_open_roles = (bool) ($payload['only_open_roles'] ?? true);
        $this->role_ids = array_values(array_map('intval', (array) ($payload['role_ids'] ?? [])));
        $legacyRole = (string) ($payload['role_id'] ?? '0');
        if ($legacyRole !== '0' && $this->role_ids === []) {
            $this->role_ids = [(int) $legacyRole];
        }
        $this->search();
    }

    private function currentFilters(): array
    {
        return [
            'q' => $this->q,
            'hackaton_id' => $this->hackaton_id,
            'role_ids' => $this->role_ids,
            'skills' => $this->skills,
            'start_from' => $this->start_from,
            'sort' => $this->sort,
            'team_size' => $this->team_size,
            'catalog_tab' => $this->catalog_tab,
            'only_open_roles' => $this->only_open_roles,
        ];
    }

    private function trackListEvent(string $eventName, array $payload = []): void
    {
        ListAnalyticsEvent::query()->create([
            'user_id' => Auth::id(),
            'list_key' => 'teams',
            'event_name' => $eventName,
            'payload' => $payload,
        ]);
    }
}
?>

@php
    $roleIdInts = $this->normalizedRoleIds();
    $hasFilters =
        filled($q)
        || $hackaton_id !== '0'
        || $roleIdInts !== []
        || ! empty($skills)
        || filled($start_from)
        || $sort !== 'newest'
        || $team_size !== 'all'
        || ($catalog_tab === 'all' && ! $only_open_roles);
    $loadingTargets =
        'search,clearFilters,setCatalogTab,toggleRoleId,saveCurrentFilter,applySavedFilter,q,hackaton_id,skills,start_from,sort,team_size,catalog_tab,only_open_roles,role_ids,nextPage,previousPage,gotoPage,setPage';
@endphp

<div class="space-y-8">
    @php
        $totalTeams = $this->teams->total();
        $tc = $totalTeams % 100;
        $tn = $totalTeams % 10;
        $teamsWord = match (true) {
            $tc >= 11 && $tc <= 19 => 'команд',
            $tn === 1 => 'команда',
            $tn >= 2 && $tn <= 4 => 'команды',
            default => 'команд',
        };
    @endphp
    <section class="relative overflow-hidden rounded-3xl border border-base-300 bg-base-200/50 px-5 py-8 sm:px-8 sm:py-10">
        <div class="pointer-events-none absolute inset-0 opacity-60" aria-hidden="true">
            <div class="absolute -top-24 -right-16 h-64 w-64 rounded-full bg-secondary/30 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-primary/25 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/3 h-40 w-40 -translate-y-1/2 rounded-full bg-accent/20 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="min-w-0 space-y-3">
                <div class="inline-flex items-center gap-2 rounded-full border border-secondary/30 bg-secondary/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-secondary">
                    <x-app-icon icon="heroicons:user-group" class="h-3.5 w-3.5" />
                    Каталог команд
                </div>
                @if ($catalog_tab === 'open')
                    <h1 class="font-display text-3xl font-black tracking-tight text-base-content sm:text-4xl lg:text-5xl">
                        <span class="bg-linear-to-r from-secondary via-accent to-primary bg-clip-text text-transparent">
                            Открытые команды
                        </span>
                    </h1>
                    <p class="max-w-2xl text-base text-base-content/70">Команды, которые сейчас ищут участников.</p>
                @else
                    <h1 class="font-display text-3xl font-black tracking-tight text-base-content sm:text-4xl lg:text-5xl">
                        <span class="bg-linear-to-r from-secondary via-accent to-primary bg-clip-text text-transparent">
                            Все команды
                        </span>
                    </h1>
                    <p class="max-w-2xl text-base text-base-content/70">Каталог публичных команд на платформе.</p>
                @endif
                <p class="text-sm font-medium tabular-nums text-base-content/60">
                    Найдено {{ $totalTeams }} {{ $teamsWord }}
                </p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row sm:items-center">
                <div
                    class="tabs tabs-boxed w-fit max-w-full gap-1 overflow-x-auto rounded-xl bg-base-100/80 p-1 backdrop-blur-sm"
                    role="tablist"
                    aria-label="Режим каталога команд"
                >
                    <button
                        type="button"
                        role="tab"
                        class="tab tab-sm sm:tab-md whitespace-nowrap {{ $catalog_tab === 'open' ? 'tab-active bg-primary! text-primary-content!' : '' }}"
                        aria-selected="{{ $catalog_tab === 'open' ? 'true' : 'false' }}"
                        wire:click="setCatalogTab('open')"
                    >
                        Открытые
                    </button>
                    <button
                        type="button"
                        role="tab"
                        class="tab tab-sm sm:tab-md whitespace-nowrap {{ $catalog_tab === 'all' ? 'tab-active bg-primary! text-primary-content!' : '' }}"
                        aria-selected="{{ $catalog_tab === 'all' ? 'true' : 'false' }}"
                        wire:click="setCatalogTab('all')"
                    >
                        Все команды
                    </button>
                </div>
                <a href="/teams/create" wire:navigate class="btn btn-primary gap-2 shadow-lg shadow-primary/20">
                    <x-app-icon icon="heroicons:plus-circle" class="h-5 w-5" />
                    Создать команду
                </a>
            </div>
        </div>
    </section>

    <section class="space-y-4" aria-label="Фильтры">
        <div class="flex flex-col gap-4 rounded-2xl border border-base-300 bg-base-200/30 p-4 sm:p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-end">
                <label class="form-control w-full min-w-0 flex-1 lg:max-w-md">
                    <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Поиск</span></span>
                    <input
                        type="search"
                        class="input input-bordered input-sm w-full border-base-300 bg-base-100 sm:input-md"
                        placeholder="Название или описание…"
                        autocomplete="off"
                        wire:model.live.debounce.300ms="q"
                    />
                </label>

                @if ($catalog_tab === 'all')
                    <label class="label cursor-pointer justify-start gap-3 rounded-xl border border-base-300 bg-base-100 px-3 py-2 lg:shrink-0">
                        <input
                            type="checkbox"
                            class="checkbox checkbox-primary checkbox-sm"
                            wire:model.live="only_open_roles"
                            aria-describedby="only-open-roles-hint"
                        />
                        <span class="label-text max-w-56 text-sm leading-snug" id="only-open-roles-hint">
                            Только с открытыми ролями
                        </span>
                    </label>
                @endif
            </div>

            <div class="flex flex-col gap-2">
                <span class="text-xs font-medium uppercase tracking-wide text-base-content/60">Роли</span>
                <div class="-mx-1 flex snap-x snap-mandatory gap-2 overflow-x-auto px-1 pb-1" role="group" aria-label="Фильтр по ролям">
                    @foreach ($this->rolesForChips as $chipRole)
                        @php $pressed = in_array($chipRole->id, $roleIdInts, true); @endphp
                        <button
                            type="button"
                            class="btn btn-sm shrink-0 snap-start border-base-300 {{ $pressed ? 'btn-primary' : 'btn-ghost bg-base-100' }}"
                            wire:click="toggleRoleId({{ $chipRole->id }})"
                            aria-pressed="{{ $pressed ? 'true' : 'false' }}"
                        >
                            {{ $chipRole->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <label class="form-control w-full min-w-0 sm:col-span-2 xl:col-span-2">
                    <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Размер команды</span></span>
                    <select class="select select-bordered select-sm w-full border-base-300 bg-base-100 sm:select-md" wire:model.live="team_size">
                        <option value="all">Любой</option>
                        <option value="2">От 2 ролей</option>
                        <option value="3">От 3 ролей</option>
                        <option value="5">От 5 ролей</option>
                    </select>
                </label>
                <label class="form-control w-full min-w-0">
                    <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Сортировка</span></span>
                    <select class="select select-bordered select-sm w-full border-base-300 bg-base-100 sm:select-md" wire:model.live="sort">
                        <option value="newest">Сначала новые</option>
                        <option value="start_soonest">Ближайший старт</option>
                    </select>
                </label>
                <div class="form-control w-full min-w-0 sm:col-span-2 xl:col-span-1">
                    <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Навыки</span></span>
                    <x-marychoices-offline
                        wire:model.live="skills"
                        :options="$this->skillsData"
                        placeholder="Выберите навыки…"
                        clearable
                        searchable
                    />
                </div>
            </div>

            <details class="group rounded-xl border border-dashed border-base-300 bg-base-100/50 open:border-primary/20">
                <summary class="cursor-pointer list-none px-3 py-2 text-sm font-medium text-primary marker:content-none [&::-webkit-details-marker]:hidden">
                    <span class="inline-flex items-center gap-2">
                        <x-app-icon icon="heroicons:funnel" class="h-4 w-4" />
                        Ещё фильтры
                        <span class="text-base-content/50 group-open:rotate-180">▼</span>
                    </span>
                </summary>
                <div class="grid grid-cols-1 gap-4 border-t border-base-300/60 p-4 sm:grid-cols-2">
                    <label class="form-control w-full">
                        <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Хакатон</span></span>
                        <select class="select select-bordered select-sm w-full sm:select-md" wire:model.live="hackaton_id">
                            @foreach ($this->hackatons as $opt)
                                <option value="{{ $opt['id'] }}">{{ $opt['name'] }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-control w-full">
                        <span class="label py-0 pb-1"><span class="label-text text-xs font-medium uppercase tracking-wide text-base-content/60">Начало хакатона от</span></span>
                        <input type="datetime-local" class="input input-bordered input-sm w-full sm:input-md" wire:model.live="start_from" />
                    </label>
                </div>
            </details>

            <div class="flex flex-wrap gap-2">
                <button type="button" class="btn btn-ghost btn-sm" wire:click="clearFilters">Сбросить фильтры</button>
            </div>
        </div>

        @if ($hasFilters)
            <div class="card card-border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    <p class="text-sm font-medium">Активные фильтры</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if (filled($q))
                            <span class="badge badge-primary badge-outline">{{ $q }}</span>
                        @endif
                        @if ($hackaton_id !== '0')
                            @php $selectedHackaton = collect($this->hackatons)->firstWhere('id', $hackaton_id); @endphp
                            <span class="badge badge-primary badge-outline">Хакатон: {{ $selectedHackaton['name'] ?? '' }}</span>
                        @endif
                        @foreach ($roleIdInts as $rid)
                            @php $rn = $this->rolesForChips->firstWhere('id', $rid); @endphp
                            @if ($rn)
                                <span class="badge badge-primary badge-outline">Роль: {{ $rn->name }}</span>
                            @endif
                        @endforeach
                        @foreach ($this->skillsData->whereIn('id', $skills) as $skill)
                            <span class="badge badge-primary badge-outline">{{ $skill->name }}</span>
                        @endforeach
                        @if (filled($start_from))
                            <span class="badge badge-primary badge-outline">Старт от: {{ Carbon::parse($start_from)->format('d.m.Y H:i') }}</span>
                        @endif
                        @if ($team_size !== 'all')
                            <span class="badge badge-primary badge-outline">Размер: от {{ $team_size }} ролей</span>
                        @endif
                        @if ($catalog_tab === 'all' && ! $only_open_roles)
                            <span class="badge badge-secondary badge-outline">Включены команды без вакансий</span>
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

    <div wire:loading class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3" wire:target="{{ $loadingTargets }}">
        @foreach (range(1, 6) as $_)
            <x-team-card-skeleton />
        @endforeach
    </div>

    <div wire:loading.remove wire:target="{{ $loadingTargets }}">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($this->teams as $team)
                @php
                    $vacantRoleNames = $team->roles
                        ->whereNull('user_id')
                        ->pluck('role.name')
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();
                    $skillTags = $team->roles
                        ->whereNull('user_id')
                        ->flatMap(fn ($r) => $r->skills)
                        ->unique('id')
                        ->take(5)
                        ->pluck('name')
                        ->filter()
                        ->values()
                        ->all();
                    $canQuickApply =
                        auth()->check()
                        && ! auth()->user()->isOrganizer()
                        && (int) $team->user_id !== auth()->id();
                    $participantUsers = $team->roles
                        ->filter(fn ($r) => $r->user_id && $r->relationLoaded('user') && $r->user)
                        ->map(fn ($r) => $r->user)
                        ->unique('id')
                        ->values()
                        ->take(4);
                @endphp
                <div
                    wire:key="team-wrap-{{ $team->id }}"
                    class="motion-safe:animate-card-enter"
                    style="animation-delay: {{ ($loop->index % 9) * 40 }}ms;"
                >
                    <x-team-card
                        :team="$team"
                        :can-quick-apply="$canQuickApply"
                        :vacant-role-names="$vacantRoleNames"
                        :skill-tags="$skillTags"
                        :participant-users="$participantUsers"
                    />
                </div>
            @empty
                <div class="col-span-full">
                    <div class="mx-auto flex max-w-lg flex-col items-center gap-6 rounded-3xl border border-base-300 bg-base-100 px-6 py-12 text-center shadow-sm">
                        <div class="text-primary" aria-hidden="true">
                            <svg class="h-40 w-full max-w-56" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="tg" x1="0" y1="0" x2="200" y2="160" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="currentColor" stop-opacity="0.35" />
                                        <stop offset="1" stop-color="currentColor" stop-opacity="0.05" />
                                    </linearGradient>
                                </defs>
                                <rect width="200" height="160" rx="16" fill="url(#tg)" />
                                <path d="M20 40h160M20 80h160M20 120h160" stroke="currentColor" stroke-opacity="0.2" stroke-width="1" />
                                <path d="M40 20v120M80 20v120M120 20v120M160 20v120" stroke="currentColor" stroke-opacity="0.15" stroke-width="1" />
                                <circle cx="100" cy="78" r="28" stroke="currentColor" stroke-opacity="0.5" stroke-width="2" fill="none" />
                                <path d="M88 78l8 8 16-16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="space-y-2">
                            <h2 class="font-display text-xl font-semibold text-base-content">{{ __('ui.teams.empty_title') }}</h2>
                            <p class="text-sm leading-relaxed text-base-content/70">
                                {{ __('ui.teams.empty_description') }}
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <a href="/teams/create" wire:navigate class="btn btn-primary">{{ __('ui.teams.create_team') }}</a>
                            <button type="button" class="btn btn-outline" wire:click="clearFilters">{{ __('ui.teams.reset_filters') }}</button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($this->teams->isNotEmpty())
            <div class="mt-6">{{ $this->teams->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </div>
</div>
