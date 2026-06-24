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
                'scores' => fn ($query) => $query->where('reviewed_by', $userId),
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
    <section class="ui-page-header">
        <div class="flex flex-col gap-4 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="text-sm breadcrumbs">
                    <ul>
                        <li><a href="{{ route('judge.dashboard') }}">Судья</a></li>
                        <li><a href="{{ route('judge.hackatons.show', $hackaton) }}">{{ $hackaton->title }}</a></li>
                        <li class="opacity-70">{{ $case->title }}</li>
                    </ul>
                </div>
                <h1 class="ui-heading-display text-2xl font-bold">Оценивание: {{ $case->title }}</h1>
                <p class="mt-0.5 text-sm text-base-content/70">Оценено {{ $ratedCount }} из {{ $totalCount }} решений.</p>
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
    </section>

    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Команда</th>
                    <th>Отправлено</th>
                    <th>Статус</th>
                    <th>Оценка</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    @php
                        $myScore = $submission->scores->first();
                        $badge = $myScore?->is_final ? ['badge-success','Final'] : ($myScore ? ['badge-warning','Draft'] : ['badge-ghost','Не оценено']);
                        $title = $submission->team?->title ?? ($submission->user?->nickname ?? $submission->user?->email ?? 'Личное решение');
                    @endphp
                    <tr>
                        <td class="font-medium">{{ $title }}</td>
                        <td class="text-sm text-base-content/70">{{ $submission->submitted_at?->format('d.m.Y H:i') }}</td>
                        <td><span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span></td>
                        <td>
                            @if($myScore)
                                {{ $myScore->score }} / {{ $myScore->max_score }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('judge.submissions.evaluate', $submission) }}" class="btn btn-sm btn-neutral">Открыть</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center opacity-70 py-8">Сдач не найдено по выбранным фильтрам.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

