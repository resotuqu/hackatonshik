<?php

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

    #[Computed]
    public function hackatons()
    {
        return Hackaton::query()
            ->select(['id', 'title', 'image_url', 'start_at', 'end_at', 'is_public', 'status'])
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
            ->when($this->public_only, fn (Builder $query) => $query->where('is_public', true))
            ->when($this->sort === 'start_soonest', fn (Builder $query) => $query->orderBy('start_at')->orderByDesc('id'))
            ->when($this->sort === 'newest', fn (Builder $query) => $query->orderByDesc('id'))
            ->paginate(5);
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
        $this->reset(['q', 'start_at', 'status', 'public_only', 'sort']);
        $this->status = 'all';
        $this->sort = 'newest';
        $this->public_only = false;
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

    public string $saved_filter_name = '';
}
?>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:items-start">

    <x-mary-card class="card card-border border-base-300 h-fit shadow-sm transition-shadow duration-200 hover:border-primary/25 hover:shadow-md lg:col-span-1">
        <h5 class="text-2xl">Фильтрация</h5>
        <x-maryform wire:submit="search">
            @csrf
            <x-mary-input label="Наименование" placeholder="Введите название..." wire:model="q"/>
            <x-marydatetime wire:model="start_at" label="Начало от"  />
            <x-maryselect label="Статус" wire:model="status" :options="[
                ['id' => 'all', 'name' => 'Любой'],
                ['id' => HackatonStatus::DRAFT->value, 'name' => HackatonStatus::DRAFT->label()],
                ['id' => HackatonStatus::PUBLISHED->value, 'name' => HackatonStatus::PUBLISHED->label()],
                ['id' => HackatonStatus::REGISTRATION_OPEN->value, 'name' => HackatonStatus::REGISTRATION_OPEN->label()],
                ['id' => HackatonStatus::IN_PROGRESS->value, 'name' => HackatonStatus::IN_PROGRESS->label()],
                ['id' => HackatonStatus::JUDGING->value, 'name' => HackatonStatus::JUDGING->label()],
                ['id' => HackatonStatus::FINISHED->value, 'name' => HackatonStatus::FINISHED->label()],
                ['id' => HackatonStatus::ARCHIVED->value, 'name' => HackatonStatus::ARCHIVED->label()],
            ]" />
            <x-maryselect label="Сортировка" wire:model="sort" :options="[
                ['id' => 'newest', 'name' => 'Сначала новые'],
                ['id' => 'start_soonest', 'name' => 'Ближайший старт'],
            ]" />
            <x-marytoggle label="Только публичные" wire:model="public_only" />
            <x-slot:actions>
                <x-mary-button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="search">Искать</x-mary-button>
                <x-mary-button class="btn-secondary" type="button" wire:click="clearFilters" wire:loading.attr="disabled" wire:target="clearFilters">Сбросить</x-mary-button>
            </x-slot:actions>
        </x-maryform>
    </x-mary-card>

    <div class="lg:col-span-2 space-y-4">
        @php
            $hasFilters = filled($q) || filled($start_at) || $status !== 'all' || $sort !== 'newest' || $public_only;
        @endphp

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

        @if ($hasFilters)
            <div class="card card-border bg-base-100">
                <div class="card-body p-4">
                    <p class="text-sm font-medium">Активные фильтры</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @if (filled($q))
                            <x-marybadge class="badge-primary" value="Наименование: {{ $q }}" />
                        @endif
                        @if (filled($start_at))
                            <x-marybadge class="badge-primary" value="Начало от: {{ Carbon::parse($start_at)->format('d.m.Y H:i') }}" />
                        @endif
                        @if ($status !== 'all')
                            <x-marybadge class="badge-primary" value="Статус: {{ str_replace('_', ' ', $status) }}" />
                        @endif
                        @if ($public_only)
                            <x-marybadge class="badge-primary" value="Только публичные" />
                        @endif
                    </div>
                    <div class="mt-3">
                        <x-mary-button class="btn-sm btn-ghost" wire:click="clearFilters">Очистить все</x-mary-button>
                    </div>
                </div>
            </div>
        @endif

        <div wire:loading.flex wire:target="search,clearFilters,q,start_at,status,sort,public_only,nextPage,previousPage,gotoPage,setPage"
             class="items-center justify-center rounded-xl border border-dashed border-base-300 bg-base-100 px-6 py-10 text-base-content/70">
            Загружаем хакатоны...
        </div>

        <div wire:loading.remove wire:target="search,clearFilters,q,start_at,status,sort,public_only,nextPage,previousPage,gotoPage,setPage">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @forelse($this->hackatons as $hackaton)
                    <x-mary-card class="card card-border border-base-300 h-full shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-lg" wire:key="hackaton-card-{{ $hackaton->id }}">
                        @php
                            $hackatonImage = filled($hackaton->image_url)
                                ? (str_starts_with($hackaton->image_url, 'http') ? $hackaton->image_url : asset('storage/' . $hackaton->image_url))
                                : null;
                        @endphp
                        <div class="overflow-hidden rounded-xl bg-base-200 aspect-video">
                            @if ($hackatonImage)
                                <img src="{{ $hackatonImage }}" class="w-full h-full object-cover" alt="{{ $hackaton->title }}">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-base-content/60">Изображение хакатона отсутствует</div>
                            @endif
                        </div>
                        <div class="mt-2 flex grow flex-col space-y-2">
                            <p class="card-title">{{$hackaton->title}}</p>
                            <x-mary-card class="card card-border bg-base-200">
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <x-marybadge class="badge-neutral" value="Команд: {{ $hackaton->teams_count }}" />
                                    <x-marybadge class="badge-neutral" value="Участников: {{ $hackaton->participants_count }}" />
                                    <x-marybadge class="badge-primary" value="Статус: {{ $hackaton->status->label() }}" />
                                </div>
                                <p>Даты проведения:
                                    {{ Carbon::parse($hackaton->start_at)->format('d.m.Y H:i') }} &DownLeftVectorBar;
                                    {{ Carbon::parse($hackaton->end_at)->format('d.m.Y H:i') }}</p>
                            </x-mary-card>
                        </div>

                        <x-slot:actions class="mt-auto pt-2">
                            <x-mary-button class="btn-primary" wire:click="openHackaton({{ $hackaton->id }})" label="Подробнее"/>
                            @auth
                                <x-mary-button class="btn-secondary" wire:click="quickApplyHackaton({{ $hackaton->id }})" label="Подать заявку"/>
                            @endauth
                        </x-slot:actions>

                    </x-mary-card>
                @empty
                    <div class="sm:col-span-2 card card-border bg-base-100">
                        <div class="card-body items-center text-center">
                            <h3 class="card-title">Хакатоны не найдены</h3>
                            <p class="text-base-content/70">
                                Попробуйте изменить параметры поиска или сбросить фильтры.
                            </p>
                            <div class="flex gap-2 mt-2">
                                <x-mary-button class="btn-outline btn-sm" wire:click="$set('public_only', true); search();">
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
            {{$this->hackatons->links(data: ['scrollTo' => false])}}
        </div>
    </div>

</div>
