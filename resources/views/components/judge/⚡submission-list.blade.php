<?php

use App\Models\Hackaton;
use App\Models\HackatonCase;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    public Hackaton $hackaton;

    public HackatonCase $case;

    #[Url]
    public string $status = 'all'; // all|rated|unrated

    #[Url]
    public string $team = '';

    /**
     * @var Collection<int, HackatonCaseSubmission>
     */
    public Collection $submissions;

    public int $ratedCount = 0;

    public int $totalCount = 0;

    public function mount(Hackaton $hackaton, HackatonCase $case): void
    {
        $this->hackaton = $hackaton;
        $this->case = $case;

        abort_unless($this->case->hackaton_id === $this->hackaton->id, 404);
        abort_unless(auth()->check(), 403);
        abort_unless($hackaton->isJudge(auth()->user()) || (int) $hackaton->user_id === (int) auth()->id(), 403);

        $this->loadSubmissions();
    }

    public function updatedStatus(): void
    {
        $this->loadSubmissions();
    }

    public function updatedTeam(): void
    {
        $this->loadSubmissions();
    }

    private function loadSubmissions(): void
    {
        $userId = (int) auth()->id();

        $base = HackatonCaseSubmission::query()
            ->where('hackaton_case_id', $this->case->id)
            ->with([
                'team:id,title,image_url',
                'user:id,nickname,email',
                'scores' => fn (Builder $query) => $query->where('reviewed_by', $userId),
            ])
            ->latest('submitted_at');

        if ($this->team !== '') {
            $base->whereHas('team', fn (Builder $query) => $query->where('title', 'like', '%'.$this->team.'%'));
        }

        if ($this->status === 'rated') {
            $base->whereHas('scores', fn (Builder $query) => $query->where('reviewed_by', $userId)->where('is_final', true));
        } elseif ($this->status === 'unrated') {
            $base->whereDoesntHave('scores', fn (Builder $query) => $query->where('reviewed_by', $userId)->where('is_final', true));
        }

        $this->submissions = $base->get();
        $this->totalCount = (int) HackatonCaseSubmission::query()->where('hackaton_case_id', $this->case->id)->count();
        $this->ratedCount = (int) HackatonCaseScore::query()
            ->whereIn('hackaton_case_submission_id', HackatonCaseSubmission::query()->where('hackaton_case_id', $this->case->id)->pluck('id'))
            ->where('reviewed_by', $userId)
            ->where('is_final', true)
            ->count();
    }
};

?>

<div class="mx-auto w-full max-w-7xl space-y-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <div class="text-sm breadcrumbs">
                <ul>
                    <li><a href="{{ route('judge.dashboard') }}">Судья</a></li>
                    <li><a href="{{ route('judge.hackatons.show', $hackaton) }}">{{ $hackaton->title }}</a></li>
                    <li class="opacity-70">{{ $case->title }}</li>
                </ul>
            </div>
            <h1 class="text-2xl font-bold">Оценивание: {{ $case->title }}</h1>
            <p class="text-sm text-base-content/70">Оценено {{ $ratedCount }} из {{ $totalCount }} решений.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
            <select class="select select-bordered select-sm" wire:model.live="status">
                <option value="all">Все</option>
                <option value="rated">Оценено</option>
                <option value="unrated">Не оценено</option>
            </select>
            <input class="input input-bordered input-sm" wire:model.live.debounce.400ms="team" placeholder="Фильтр по команде">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($submissions as $submission)
            @php
                $myScore = $submission->scores->first();
                $badge = $myScore?->is_final ? ['badge-success','Final'] : ($myScore ? ['badge-warning','Draft'] : ['badge-ghost','—']);
                $title = $submission->team?->title ?? ($submission->user?->nickname ?? $submission->user?->email ?? 'Личное решение');
            @endphp
            <a href="{{ route('judge.submissions.evaluate', $submission) }}"
               class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="card-body gap-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-semibold truncate">{{ $title }}</div>
                            <div class="text-xs text-base-content/60">{{ $submission->submitted_at?->format('d.m.Y H:i') }}</div>
                        </div>
                        <span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="text-base-content/70">
                            @if($myScore)
                                {{ $myScore->score }} / {{ $myScore->max_score }}
                            @else
                                Нет оценки
                            @endif
                        </div>
                        <span class="btn btn-sm btn-primary">Открыть</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

