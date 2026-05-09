<?php

namespace App\Livewire\Pages\Teams;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\ListAnalyticsEvent;
use App\Models\Role;
use App\Models\SavedListFilter;
use App\Models\Skill;
use App\Models\Team;
use App\Models\TeamApplication;
use App\Models\TeamRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app', [
    'title' => 'Команды участников — Хакатонщик',
    'meta_description' => 'Публичные команды для участия в хакатонах: находите команду мечты или собирайте свою.',
    'canonical_url' => '/teams',
])]
class Index extends Component
{
    use WithPagination;

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
    public function normalizedRoleIds(): array
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

        $buildQuery = fn () => Team::query()
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
            ->withCount(['roles as participants_count' => fn ($query) => $query->whereNotNull('user_id')])

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

        if (! app()->isProduction()) {
            return $buildQuery();
        }

        $cacheKey = sprintf(
            'livewire:teams:index:v1:p%s:q%s:sort%s:tab%s',
            $this->getPage(),
            md5(json_encode($this->currentFilters(), JSON_THROW_ON_ERROR)),
            $this->sort,
            $this->catalog_tab
        );
        $cache = Cache::supportsTags() ? Cache::tags(['catalog', 'catalog:teams']) : Cache::store();

        return $cache->remember($cacheKey, now()->addMinutes(2), $buildQuery);
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
            $this->role_ids = [...$ids, $roleId];
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

        $role = $team->roles->first(function ($candidate): bool {
            return $candidate instanceof TeamRole && $candidate->user_id === null;
        });
        if (! $role instanceof TeamRole) {
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

        $decoded = json_decode((string) $filter->filters, true);
        $payload = is_array($decoded) ? $decoded : [];
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
        $legacyRole = (int) ($payload['role_id'] ?? 0);
        if ($legacyRole > 0 && $this->role_ids === []) {
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

    public function render()
    {
        return view('pages.teams.index');
    }
}
