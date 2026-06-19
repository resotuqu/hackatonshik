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

<div class="space-y-6">
    <x-recommended-teams :recommendations="$this->recommendedTeams->all()" />

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

    <section class="ui-page-header">
        <div class="flex flex-col gap-4 pb-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0 space-y-2">
                <p class="text-sm text-base-content/60">Каталог команд</p>
                <h1 class="ui-heading-display text-3xl font-bold sm:text-4xl">Команды</h1>
                <p class="max-w-2xl text-base text-base-content/70">Команды участников хакатонов — открытые и закрытые.</p>
                <div class="flex flex-wrap items-center gap-3">
                    <p class="text-sm font-medium tabular-nums text-base-content/60">
                        {{ $totalTeams }} {{ $teamsWord }}
                    </p>
                    <div class="flex gap-1 rounded-full bg-base-200 p-1" role="tablist" aria-label="Режим каталога команд">
                        <button
                            type="button"
                            role="tab"
                            class="rounded-full px-4 py-1 text-sm font-medium whitespace-nowrap transition-all duration-200 {{ $catalog_tab === 'open' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/60 hover:text-base-content' }}"
                            aria-selected="{{ $catalog_tab === 'open' ? 'true' : 'false' }}"
                            wire:click="setCatalogTab('open')"
                        >
                            Открытые
                        </button>
                        <button
                            type="button"
                            role="tab"
                            class="rounded-full px-4 py-1 text-sm font-medium whitespace-nowrap transition-all duration-200 {{ $catalog_tab === 'all' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/60 hover:text-base-content' }}"
                            aria-selected="{{ $catalog_tab === 'all' ? 'true' : 'false' }}"
                            wire:click="setCatalogTab('all')"
                        >
                            Все
                        </button>
                    </div>
                </div>
            </div>
            <a href="/teams/create" wire:navigate class="ui-cta-primary shrink-0 self-start">
                <x-app-icon icon="heroicons:plus-circle" class="h-5 w-5" />
                Создать команду
            </a>
        </div>
    </section>

    <section aria-label="Фильтры">
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
                            class="btn btn-sm shrink-0 snap-start {{ $pressed ? 'btn-primary' : 'btn-ghost border border-base-300 bg-base-100 hover:border-primary/40' }}"
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

            <div class="grid grid-cols-1 gap-4 border-t border-base-300/50 pt-4 sm:grid-cols-2">
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
                    <input type="date" class="input input-bordered input-sm w-full sm:input-md" wire:model.live="start_from" />
                </label>
            </div>

            <div class="flex flex-wrap gap-2">
                @if ($hasFilters)
                    <button type="button" class="btn btn-ghost btn-sm gap-1.5" wire:click="clearFilters">
                        <x-app-icon icon="heroicons:arrow-path" class="h-4 w-4" />
                        Сбросить фильтры
                    </button>
                @endif
            </div>
        </div>

        @if ($hasFilters)
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium text-base-content/50">Фильтры:</span>
                @if (filled($q))
                    <span class="badge badge-outline border-base-300 text-base-content/80">{{ $q }}</span>
                @endif
                @if ($hackaton_id !== '0')
                    @php $selectedHackaton = collect($this->hackatons)->firstWhere('id', $hackaton_id); @endphp
                    <span class="badge badge-outline border-base-300 text-base-content/80">Хакатон: {{ $selectedHackaton['name'] ?? '' }}</span>
                @endif
                @foreach ($roleIdInts as $rid)
                    @php $rn = $this->rolesForChips->firstWhere('id', $rid); @endphp
                    @if ($rn)
                        <span class="badge badge-outline border-base-300 text-base-content/80">Роль: {{ $rn->name }}</span>
                    @endif
                @endforeach
                @foreach ($this->skillsData->whereIn('id', $skills) as $skill)
                    <span class="badge badge-outline border-base-300 text-base-content/80">{{ $skill->name }}</span>
                @endforeach
                @if (filled($start_from))
                    <span class="badge badge-outline border-base-300 text-base-content/80">Старт от: {{ \Illuminate\Support\Carbon::parse($start_from)->format('d.m.Y') }}</span>
                @endif
                @if ($team_size !== 'all')
                    <span class="badge badge-outline border-base-300 text-base-content/80">Размер: от {{ $team_size }} ролей</span>
                @endif
                @if ($catalog_tab === 'all' && ! $only_open_roles)
                    <span class="badge badge-outline border-base-300 text-base-content/80">Все команды</span>
                @endif
            </div>
        @endif

        @auth
            <div class="mt-3 card card-border border-base-300 bg-base-100">
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
                        && auth()->user()->canParticipate()
                        && (int) $team->user_id !== auth()->id();
                    $participantUsers = $team->roles
                        ->filter(fn ($r) => $r->user_id && $r->relationLoaded('user') && $r->user)
                        ->map(fn ($r) => $r->user)
                        ->unique('id')
                        ->values()
                        ->take(4);
                @endphp
                <div wire:key="team-wrap-{{ $team->id }}">
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
                    <x-empty-state
                        :title="__('ui.teams.empty_title')"
                        :description="__('ui.teams.empty_description')"
                        icon="heroicons:user-group"
                        :actionHref="'/teams/create'"
                        :actionLabel="__('ui.teams.create_team')"
                        :secondaryActionHref="'javascript:void(0)'"
                        :secondaryActionLabel="__('ui.teams.reset_filters')"
                        @click="$wire.clearFilters()"
                    />
                </div>
            @endforelse
        </div>

        @if ($this->teams->isNotEmpty())
            <div class="mt-6">{{ $this->teams->links(data: ['scrollTo' => false]) }}</div>
        @endif
    </div>
</div>
