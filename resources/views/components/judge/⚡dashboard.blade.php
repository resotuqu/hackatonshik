<?php

use App\Actions\Judge\BuildJudgeHackatonScoringSummary;
use App\Models\Hackaton;
use App\Support\PublicStorageUrl;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

new class extends Component {
    /**
     * @var \Illuminate\Support\Collection<int, Hackaton>
     */
    public $hackatons;

    /**
     * @var array<int, array{total: int, rated: int}>
     */
    public array $progress = [];

    public function mount(BuildJudgeHackatonScoringSummary $summary): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $this->hackatons = Hackaton::query()
            ->whereHas('judgeAssignments', fn (Builder $query) => $query->where('user_id', $user->id))
            ->withCount(['cases'])
            ->latest('start_at')
            ->get(['id', 'title', 'image_url', 'status', 'start_at', 'end_at']);

        $this->progress = $this->hackatons->mapWithKeys(function ($hackaton) use ($summary, $user) {
            $data = $summary->handle($hackaton, $user);

            return [$hackaton->id => [
                'total' => $data['totalSubmissions'],
                'rated' => $data['ratedSubmissions'],
            ]];
        })->all();
    }
};

?>

<div class="mx-auto w-full max-w-6xl space-y-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="ui-heading-display text-2xl font-bold sm:text-3xl lg:text-4xl">Судья</h1>
            <p class="mt-1 text-sm text-base-content/70">Оценка проектов и экспертиза. Назначенные хакатоны и быстрый переход к оцениванию.</p>
        </div>
        <a href="{{ route('hackatons.index') }}" class="ui-cta-outline btn-sm shrink-0 self-start gap-2 sm:self-auto" wire:navigate>
            <x-app-icon icon="heroicons:trophy" class="h-4 w-4" />
            Каталог событий
        </a>
    </header>

    @if ($hackatons->isEmpty())
        <x-empty-state
            embedded
            title="Пока нет назначенных хакатонов"
            description="Организатор ещё не назначил вас судьёй ни на один хакатон."
            icon="heroicons:scale"
            test-id="judge-dashboard-empty"
        />
    @else
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($hackatons as $hackaton)
                <a href="{{ route('judge.hackatons.show', $hackaton) }}"
                    class="ui-surface-card group flex flex-col gap-4 p-5 transition-shadow hover:shadow-md">
                    <div class="flex items-start gap-3">
                        @if ($hackaton->image_url)
                            <img src="{{ PublicStorageUrl::for($hackaton->image_url) }}" alt=""
                                class="h-12 w-12 shrink-0 rounded-xl border border-base-300 object-cover">
                        @else
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-base-300 bg-base-200">
                                <x-app-icon icon="heroicons:trophy" class="h-6 w-6 text-base-content/30" />
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold leading-tight transition-colors group-hover:text-primary">{{ $hackaton->title }}</p>
                            @if ($hackaton->start_at)
                                <div class="mt-1 flex items-center gap-1 text-xs text-base-content/50">
                                    <x-app-icon icon="heroicons:calendar" class="h-3 w-3" />
                                    {{ $hackaton->start_at->translatedFormat('d M Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        $p = $progress[$hackaton->id] ?? ['total' => 0, 'rated' => 0];
                    @endphp

                    @if($p['total'] > 0)
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs text-base-content/50">
                                <span>Оценено</span>
                                <span>{{ $p['rated'] }} / {{ $p['total'] }}</span>
                            </div>
                            <progress class="progress progress-primary w-full" value="{{ $p['rated'] }}" max="{{ $p['total'] }}"></progress>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="{{ $hackaton->status->badgeClass() }} badge badge-sm">{{ $hackaton->status->label() }}</span>
                            <span class="text-xs text-base-content/50">{{ $hackaton->cases_count }} {{ $hackaton->cases_count === 1 ? 'кейс' : ($hackaton->cases_count < 5 ? 'кейса' : 'кейсов') }}</span>
                        </div>
                        <span class="btn btn-primary btn-xs">Оценивать</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
