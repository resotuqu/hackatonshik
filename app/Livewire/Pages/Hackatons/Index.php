<?php

namespace App\Livewire\Pages\Hackatons;

use App\Enums\ApplicationStatus;
use App\Enums\HackatonLevel;
use App\Enums\HackatonStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\ListAnalyticsEvent;
use App\Models\SavedListFilter;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app', [
    'title' => 'Каталог хакатонов — Хакатонщик',
    'meta_description' => 'Каталог хакатонов: онлайн и офлайн соревнования для команд разработчиков, дизайнеров и продуктовых команд.',
    'canonical_url' => '/hackatons',
])]
class Index extends Component
{
    use WithPagination;

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
        $buildQuery = fn () => Hackaton::query()
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

        if (! app()->isProduction()) {
            return $buildQuery();
        }

        $cacheKey = sprintf(
            'livewire:hackatons:index:v1:p%s:q%s:s%s:l%s:pr%s:so%s',
            $this->getPage(),
            md5(json_encode($this->currentFilters(), JSON_THROW_ON_ERROR)),
            $this->status,
            $this->level,
            (int) $this->with_prizes,
            $this->sort
        );
        $cache = Cache::supportsTags() ? Cache::tags(['catalog', 'catalog:hackatons']) : Cache::store();

        return $cache->remember($cacheKey, now()->addMinutes(2), $buildQuery);
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
            fn () => Team::query()
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
            'status' => ApplicationStatus::PENDING,
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

        $decoded = json_decode((string) $filter->filters, true);
        $payload = is_array($decoded) ? $decoded : [];
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

    public function render()
    {
        return view('pages.hackatons.index');
    }
}
