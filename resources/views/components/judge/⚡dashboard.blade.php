<?php

use App\Models\Hackaton;
use App\Support\PublicStorageUrl;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

new class extends Component {
    /**
     * @var \Illuminate\Support\Collection<int, Hackaton>
     */
    public $hackatons;

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $this->hackatons = Hackaton::query()
            ->whereHas('judgeAssignments', fn(Builder $query) => $query->where('user_id', $user->id))
            ->withCount(['cases'])
            ->latest('start_at')
            ->get(['id', 'title', 'image_url', 'status', 'start_at', 'end_at']);
    }
};

?>

<div class="mx-auto w-full max-w-6xl space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Панель судьи</h1>
            <p class="text-sm text-base-content/70">Ваши назначенные хакатоны и быстрый переход к оцениванию.</p>
        </div>
    </div>

    @if ($hackatons->isEmpty())
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <x-empty-state embedded title="Пока нет назначенных хакатонов"
                    description="Организатор ещё не назначил вас судьёй ни на один хакатон." icon="heroicons:scale" />
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($hackatons as $hackaton)
                <a href="{{ route('judge.hackatons.show', $hackaton) }}"
                    class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="card-body gap-3">
                        <div class="flex items-start gap-3">
                            @if ($hackaton->image_url)
                                <img src="{{ PublicStorageUrl::for($hackaton->image_url) }}" alt=""
                                    class="h-12 w-12 rounded-xl object-cover border border-base-200">
                            @else
                                <div class="h-12 w-12 rounded-xl bg-base-200 border border-base-200"></div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-bold truncate">{{ $hackaton->title }}</div>
                                <div class="text-xs text-base-content/60">
                                    Кейсов: {{ $hackaton->cases_count }}
                                </div>
                            </div>
                        </div>
                        <div class="card-actions justify-end">
                            <span class="btn btn-sm btn-primary">Оценивать</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
