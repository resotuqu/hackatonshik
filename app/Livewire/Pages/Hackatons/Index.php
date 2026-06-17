<?php

namespace App\Livewire\Pages\Hackatons;

use App\Enums\ApplicationStatus;
use App\Models\Hackaton;
use App\Models\HackatonApplication;
use App\Models\ListAnalyticsEvent;
use App\Models\SavedListFilter;
use App\Models\Team;
use App\Models\User;
use App\Support\FlashToast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts::app', [
    'title' => 'Каталог хакатонов — Хакатонщик',
    'meta_description' => 'Каталог хакатонов: онлайн и офлайн соревнования для команд разработчиков, дизайнеров и продуктовых команд.',
    'canonical_url' => '/hackatons',
])]
class Index extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'start_at')]
    public string $start_at = '';

    #[Url(as: 'sort')]
    public string $sort = 'newest';

    #[Url(as: 'level')]
    public string $level = 'all';

    public string $saved_filter_name = '';

    #[Computed]
    public function hackatons()
    {
        $buildQuery = fn () => Hackaton::query()
            ->select([
                'id', 'title', 'image_url', 'start_at', 'end_at', 'is_public', 'status',
                'prize_fund', 'prize_places_count', 'level', 'registration_deadline_at', 'updated_at',
            ])
            ->where('is_public', true)
            ->withCount('teams')
            ->withCount(['roles as participants_count' => fn (Builder $query) => $query->whereNotNull('team_roles.user_id')])

            ->when($this->q !== '', function (Builder $query): void {
                $query->where('title', 'like', '%'.$this->q.'%');
            })
            ->when($this->start_at !== '', function (Builder $query): void {
                $query->where('start_at', '>=', $this->start_at);
            })
            ->when($this->level !== 'all', fn (Builder $query): Builder => $query->where('level', $this->level))
            ->when($this->sort === 'start_soonest', fn (Builder $query) => $query->orderBy('start_at')->orderByDesc('id'))
            ->when($this->sort === 'newest', fn (Builder $query) => $query->orderByDesc('id'))
            ->when($this->sort === 'biggest_prize', fn (Builder $query) => $query->orderByDesc('prize_fund')->orderByDesc('id'))
            ->paginate(9);

        if (! app()->isProduction()) {
            return $buildQuery();
        }

        $cacheKey = sprintf(
            'livewire:hackatons:index:v2:p%s:q%s:l%s:so%s',
            $this->getPage(),
            hash('sha256', json_encode($this->currentFilters(), JSON_THROW_ON_ERROR)),
            $this->level,
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
        $this->reset(['q', 'start_at', 'sort', 'level']);
        $this->level = 'all';
        $this->sort = 'newest';
        $this->resetPage();
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
            $this->warning('Нет доступной команды для быстрой заявки.', position: FlashToast::POSITION);

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
        $this->success('Быстрая заявка отправлена.', position: FlashToast::POSITION);
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
        $this->sort = (string) ($payload['sort'] ?? 'newest');
        $this->level = (string) ($payload['level'] ?? 'all');
        $this->search();
    }

    private function currentFilters(): array
    {
        return [
            'q' => $this->q,
            'start_at' => $this->start_at,
            'sort' => $this->sort,
            'level' => $this->level,
        ];
    }

    private function trackListEvent(string $eventName, array $payload = []): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $userId = Auth::id();

        if ($userId !== null && ! User::query()->whereKey($userId)->exists()) {
            $userId = null;
        }

        ListAnalyticsEvent::query()->create([
            'user_id' => $userId,
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
