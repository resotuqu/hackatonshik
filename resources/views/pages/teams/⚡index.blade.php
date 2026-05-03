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
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

new #[Layout('layouts::app', ['title' => "Команды"])]
class extends Component {
    use \Livewire\WithPagination;

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
        return Team::query()
            ->select(['id', 'user_id', 'title', 'image_url', 'hackaton_id'])
            ->with(['user:id,nickname', 'hackaton:id,title,start_at,end_at', 'roles:id,team_id,role_id,user_id', 'roles.role:id,name'])
            ->withCount('roles')
            ->withCount(['roles as empty_roles_count' => fn ($query) => $query->whereNull('user_id')])
            ->whereHas('roles', function ($query) {
                $query->whereNull('user_id');
            })
            ->when($this->q !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('title', 'like', '%' . $this->q . '%')
                        ->orWhereHas('roles.skills', fn ($skillsQuery) => $skillsQuery->where('skills.name', 'like', '%' . $this->q . '%'));
                });
            })
            ->when($this->hackaton_id !== '0', function ($query) {
                $query->where('teams.hackaton_id', '=', $this->hackaton_id);
            })
            ->when($this->role_id !== '0', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('role_id', '=', $this->role_id);
                });
            })
            ->when(!empty($this->skills), function ($query) {
                $query->whereHas('roles.skills', function ($q) {
                    $q->whereIn('skills.id', $this->skills);
                });
            })
            ->when($this->team_size !== 'all', function ($query) {
                $query->has('roles', '>=', (int) $this->team_size);
            })
            ->when($this->start_from !== '', function ($query) {
                $query->whereHas('hackaton', function($q) {
                    $q->where('start_at', '>=', $this->start_from);
                });
            })
            ->when($this->sort === 'start_soonest', fn ($query) => $query->join('hackatons', 'hackatons.id', '=', 'teams.hackaton_id')->orderBy('hackatons.start_at')->select('teams.*'))
            ->when($this->sort === 'newest', fn ($query) => $query->orderByDesc('id'))
            ->paginate(6);
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

    public function search()
    {
        $this->resetPage();
        $this->trackListEvent('filter_apply', $this->currentFilters());
    }

    public function clearFilters(): void
    {
        $this->reset(['q', 'hackaton_id', 'role_id', 'skills', 'start_from', 'sort', 'team_size']);
        $this->hackaton_id = '0';
        $this->role_id = '0';
        $this->sort = 'newest';
        $this->team_size = 'all';
        $this->resetPage();
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
        $this->role_id = (string) ($payload['role_id'] ?? '0');
        $this->skills = (array) ($payload['skills'] ?? []);
        $this->start_from = (string) ($payload['start_from'] ?? '');
        $this->sort = (string) ($payload['sort'] ?? 'newest');
        $this->team_size = (string) ($payload['team_size'] ?? 'all');
        $this->search();
    }

    private function currentFilters(): array
    {
        return [
            'q' => $this->q,
            'hackaton_id' => $this->hackaton_id,
            'role_id' => $this->role_id,
            'skills' => $this->skills,
            'start_from' => $this->start_from,
            'sort' => $this->sort,
            'team_size' => $this->team_size,
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

    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'hackaton_id')]
    public string $hackaton_id = '0';

    #[Url(as: 'role_id')]
    public string $role_id = '0';

    #[Url(as: 'skills')]
    public array $skills = [];

    #[Url(as: 'start_from')]
    public string $start_from = '';

    #[Url(as: 'sort')]
    public string $sort = 'newest';

    #[Url(as: 'team_size')]
    public string $team_size = 'all';

    public string $saved_filter_name = '';
}
?>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">

    <x-mary-card class="card card-border border-base-300 h-fit shadow-sm transition-shadow duration-200 hover:border-primary/25 hover:shadow-md lg:col-span-1">
        <h5 class="text-2xl">Фильтрация</h5>
        <x-maryform wire:submit="search">

            {{--HackatonTitle--}}
            <x-mary-input label="Наименование" placeholder="Введите название..." wire:model="q"/>

            {{--    HackatonId    --}}
            <x-maryselect wire:model="hackaton_id" :options="$this->hackatons" label="Хакатон" />

            {{-- Role --}}
            <x-maryselect wire:model="role_id" :options="$this->rolesData" label="Роль" />

            {{-- Skills --}}
            <x-marychoices-offline
                label="Навыки"
                wire:model="skills"
                :options="$this->skillsData"
                placeholder="Выберите навыки..."
                clearable
                searchable />

            {{--HackatonStartFrom--}}
            <x-marydatetime wire:model="start_from" label="Начало от"  />
            <x-maryselect wire:model="team_size" :options="[
                ['id' => 'all', 'name' => 'Любой размер'],
                ['id' => '2', 'name' => 'От 2 ролей'],
                ['id' => '3', 'name' => 'От 3 ролей'],
                ['id' => '5', 'name' => 'От 5 ролей'],
            ]" label="Размер команды" />
            <x-maryselect wire:model="sort" :options="[
                ['id' => 'newest', 'name' => 'Сначала новые'],
                ['id' => 'start_soonest', 'name' => 'Ближайший старт'],
            ]" label="Сортировка" />

            <x-slot:actions>
                <x-mary-button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="search">Искать</x-mary-button>
                <x-mary-button type="button" class="btn-secondary" wire:click="clearFilters" wire:loading.attr="disabled" wire:target="clearFilters">Сбросить</x-mary-button>
            </x-slot:actions>

        </x-maryform>
    </x-mary-card>
    
    <div class="lg:col-span-2 space-y-4">
        @php
            $hasFilters = filled($q) || $hackaton_id !== '0' || $role_id !== '0' || !empty($skills) || filled($start_from) || $sort !== 'newest' || $team_size !== 'all';
        @endphp

        @if ($hasFilters)
            <div class="card card-border bg-base-100">
                <div class="card-body p-4">
                    <p class="text-sm font-medium">Активные фильтры</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if (filled($q))
                            <x-marybadge class="badge-primary" value="Наименование: {{ $q }}" />
                        @endif
                        @if ($hackaton_id !== '0')
                            @php
                                $selectedHackaton = collect($this->hackatons)->firstWhere('id', $hackaton_id);
                            @endphp
                            <x-marybadge class="badge-primary" value="Хакатон: {{ $selectedHackaton['name'] ?? 'Выбран' }}" />
                        @endif
                        @if ($role_id !== '0')
                            @php
                                $selectedRole = collect($this->rolesData)->firstWhere('id', $role_id);
                            @endphp
                            <x-marybadge class="badge-primary" value="Роль: {{ $selectedRole['name'] ?? 'Выбрана' }}" />
                        @endif
                        @if (!empty($skills))
                            @foreach ($this->skillsData->whereIn('id', $skills) as $skill)
                                <x-marybadge class="badge-primary" value="Навык: {{ $skill->name }}" />
                            @endforeach
                        @endif
                        @if (filled($start_from))
                            <x-marybadge class="badge-primary" value="Начало от: {{ Carbon::parse($start_from)->format('d.m.Y H:i') }}" />
                        @endif
                        @if ($team_size !== 'all')
                            <x-marybadge class="badge-primary" value="Размер: от {{ $team_size }} ролей" />
                        @endif
                    </div>
                    <div class="mt-3">
                        <x-mary-button class="btn-sm btn-ghost" wire:click="clearFilters">Очистить все</x-mary-button>
                    </div>
                </div>
            </div>
        @endif

        @if(auth()->check())
            <div class="card card-border bg-base-100">
                <div class="card-body p-4">
                    <p class="text-sm font-medium">Сохраненные фильтры</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @forelse($this->savedFilters as $savedFilter)
                            <x-mary-button class="btn-xs btn-outline" wire:click="applySavedFilter({{ $savedFilter->id }})">
                                {{ $savedFilter->name }}
                            </x-mary-button>
                        @empty
                            <p class="text-sm text-base-content/60">Пока нет сохраненных фильтров.</p>
                        @endforelse
                    </div>
                    <div class="mt-3 flex gap-2">
                        <x-mary-input wire:model="saved_filter_name" placeholder="Название фильтра" />
                        <x-mary-button class="btn-sm btn-primary" wire:click="saveCurrentFilter">Сохранить</x-mary-button>
                    </div>
                </div>
            </div>
        @endif

        <div wire:loading.flex wire:target="search,clearFilters,q,hackaton_id,role_id,skills,start_from,sort,team_size,nextPage,previousPage,gotoPage,setPage"
            class="items-center justify-center rounded-xl border border-dashed border-base-300 bg-base-100 px-6 py-10 text-base-content/70">
            Загружаем команды...
        </div>

        <div wire:loading.remove wire:target="search,clearFilters,q,hackaton_id,role_id,skills,start_from,sort,team_size,nextPage,previousPage,gotoPage,setPage">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @forelse($this->teams as $team)
                    <x-mary-card class="card card-border border-base-300 h-full shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-lg" wire:key="team-card-{{ $team->id }}">
                        @php
                            $teamImage = filled($team->image_url)
                                ? (str_starts_with($team->image_url, 'http') ? $team->image_url : asset('storage/' . $team->image_url))
                                : null;
                        @endphp
                        <div class="overflow-hidden rounded-xl bg-base-200 aspect-video">
                            @if ($teamImage)
                                <img src="{{ $teamImage }}" class="w-full h-full object-cover" alt="{{ $team->title }}">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-base-content/60">Изображение команды отсутствует</div>
                            @endif
                        </div>

                        <div class="mt-2 flex grow flex-col space-y-2">
                            <p class="card-title">{{ $team->title }}</p>

                            <x-mary-card class="card card-border bg-base-200">
                                <p>Пользователь: {{ $team->user->nickname }}</p>
                                <p>{{ $team->hackaton->title }}</p>
                                <p>
                                    Даты проведения:
                                    {{ Carbon::parse($team->hackaton->start_at)->format('d.m.Y H:i') }} &DownLeftVectorBar;
                                    {{ Carbon::parse($team->hackaton->end_at)->format('d.m.Y H:i') }}
                                </p>
                            </x-mary-card>

                            <div class="mt-1 flex flex-wrap gap-2">
                                <x-marybadge value="Количество ролей: {{ $team->roles_count }}" class="badge-neutral" />
                                <x-marybadge value="Свободно ролей: {{ $team->empty_roles_count }}" class="badge-neutral" />
                                @php
                                    $requiredRolesCollection = $team->roles
                                        ->whereNull('user_id')
                                        ->pluck('role.name')
                                        ->filter()
                                        ->values();
                                    $requiredRolesPreview = $requiredRolesCollection->take(2)->implode(', ');
                                    $requiredRolesOverflow = max($requiredRolesCollection->count() - 2, 0);
                                @endphp
                                @if ($requiredRolesPreview !== '')
                                    <x-marybadge
                                        value="Нужны роли: {{ $requiredRolesPreview }}{{ $requiredRolesOverflow > 0 ? ', +'.$requiredRolesOverflow : '' }}"
                                        class="badge-primary h-auto max-w-full whitespace-normal wrap-break-word py-1 text-left leading-snug"
                                    />
                                @endif
                            </div>
                        </div>

                        <x-slot:actions class="mt-auto pt-2">
                            <x-mary-button label="Подробнее" class="btn-primary" wire:click="openTeam({{ $team->id }})" />
                            @auth
                                @if(!auth()->user()->isOrganizer())
                                    <x-mary-button label="Откликнуться" class="btn-secondary" wire:click="quickApplyTeam({{ $team->id }})" />
                                @endif
                            @endauth
                        </x-slot:actions>

                    </x-mary-card>
                @empty
                    <div class="sm:col-span-2 card card-border bg-base-100">
                        <div class="card-body items-center text-center">
                            <h3 class="card-title">Команды не найдены</h3>
                            <p class="text-base-content/70">
                                Попробуйте изменить параметры поиска или сбросить фильтры.
                            </p>
                            <div class="flex gap-2 mt-2">
                                <x-mary-button class="btn-outline btn-sm" wire:click="$set('hackaton_id', '0'); $set('role_id', '0'); search();">
                                    Показать все публичные
                                </x-mary-button>
                                <x-mary-button class="btn-outline btn-sm" wire:click="$set('sort', 'start_soonest'); search();">
                                    Ближайший старт
                                </x-mary-button>
                            </div>
                            <x-mary-button class="btn-primary btn-sm mt-2" wire:click="clearFilters">
                                Сбросить фильтры
                            </x-mary-button>
                        </div>
                    </div>
                @endforelse
            </div>
            {{$this->teams->links(data: ['scrollTo' => false])}}
        </div>
    </div>

</div>
