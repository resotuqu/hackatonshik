<?php

use App\Actions\Judge\EvaluateSubmissionAction;
use App\Actions\Judge\SaveScoreDraftAction;
use App\Enums\JudgeDomain;
use App\Models\HackatonCaseScore;
use App\Models\HackatonCaseSubmission;
use App\Models\HackatonJudge;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Livewire\Component;

new class extends Component
{
    public HackatonCaseSubmission $submission;

    /**
     * @var array<string, mixed>
     */
    public array $criteriaScores = [];

    /**
     * @var array<string, string>
     */
    public array $fieldComments = [];

    public ?string $generalComment = null;

    public bool $isFinal = false;

    public bool $isSaving = false;

    public function mount(HackatonCaseSubmission $submission): void
    {
        $this->submission = HackatonCaseSubmission::query()
            ->with([
                'case.fields',
                'answers.field',
                'team:id,title',
                'user:id,nickname,email',
                'scores' => fn (Builder $query) => $query->where('reviewed_by', auth()->id()),
            ])
            ->findOrFail($submission->id);

        $this->authorize('view', $this->submission);

        $existing = $this->submission->scores->first();
        if ($existing instanceof HackatonCaseScore) {
            $this->criteriaScores = is_array($existing->criteria_scores) ? $existing->criteria_scores : [];
            $this->fieldComments = is_array($existing->field_comments) ? $existing->field_comments : [];
            $this->generalComment = $existing->general_comment;
            $this->isFinal = (bool) $existing->is_final;
        }
    }

    public function saveDraft(SaveScoreDraftAction $saveDraft): void
    {
        $this->isSaving = true;

        $saveDraft->handle(auth()->user(), $this->submission, [
            'criteria_scores' => $this->criteriaScores,
            'field_comments' => $this->fieldComments,
            'general_comment' => $this->generalComment,
        ]);

        $this->isSaving = false;

        $this->dispatch('toast', message: 'Черновик сохранён');
    }

    public function finalize(EvaluateSubmissionAction $evaluate): void
    {
        $this->isSaving = true;

        $evaluate->handle(auth()->user(), $this->submission, [
            'criteria_scores' => $this->criteriaScores,
            'field_comments' => $this->fieldComments,
            'general_comment' => $this->generalComment,
        ]);

        $this->isSaving = false;
        $this->isFinal = true;

        $this->dispatch('toast', message: 'Оценка финализирована');
    }

    public function goNext(): void
    {
        $next = HackatonCaseSubmission::query()
            ->where('hackaton_case_id', $this->submission->hackaton_case_id)
            ->where('id', '>', $this->submission->id)
            ->orderBy('id')
            ->value('id');

        if ($next) {
            $this->redirectRoute('judge.submissions.evaluate', ['submission' => $next]);
        }
    }

    public function goPrev(): void
    {
        $prev = HackatonCaseSubmission::query()
            ->where('hackaton_case_id', $this->submission->hackaton_case_id)
            ->where('id', '<', $this->submission->id)
            ->orderByDesc('id')
            ->value('id');

        if ($prev) {
            $this->redirectRoute('judge.submissions.evaluate', ['submission' => $prev]);
        }
    }

    public function judgeDomain(): JudgeDomain
    {
        $assignment = HackatonJudge::query()
            ->where('hackaton_id', $this->submission->case->hackaton_id)
            ->where('user_id', auth()->id())
            ->first();

        return $assignment?->domain instanceof JudgeDomain ? $assignment->domain : JudgeDomain::DEV;
    }
};

?>

<div
    class="mx-auto w-full max-w-7xl space-y-4"
    x-data
    x-on:keydown.window.prevent.ctrl.s="$wire.saveDraft()"
    x-on:keydown.window.prevent.arrow-right="$wire.goNext()"
    x-on:keydown.window.prevent.arrow-left="$wire.goPrev()"
>
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">
        <div>
            <div class="text-sm breadcrumbs">
                <ul>
                    <li><a href="{{ route('judge.dashboard') }}">Судья</a></li>
                    <li><a href="{{ route('judge.hackatons.show', $submission->case->hackaton_id) }}">Хакатон</a></li>
                    <li><a href="{{ route('judge.cases.submissions', [$submission->case->hackaton_id, $submission->case->id]) }}">Кейс</a></li>
                    <li class="opacity-70">Оценка</li>
                </ul>
            </div>
            <h1 class="text-2xl font-bold">
                {{ $submission->team?->title ?? ($submission->user?->nickname ?? $submission->user?->email ?? 'Личное решение') }}
            </h1>
            <p class="text-xs text-base-content/60">
                Отправлено: {{ $submission->submitted_at?->format('d.m.Y H:i') }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($isFinal)
                <span class="badge badge-success">Final</span>
            @else
                <span class="badge badge-warning">Draft</span>
            @endif
            <button class="btn btn-sm btn-outline" wire:click="goPrev">← Пред.</button>
            <button class="btn btn-sm btn-outline" wire:click="goNext">След. →</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="space-y-4">
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body space-y-3">
                    <div class="flex items-center justify-between">
                        <h2 class="card-title text-lg">Решение</h2>
                        <span class="text-xs text-base-content/60">Side-by-side</span>
                    </div>

                    @foreach($submission->answers as $answer)
                        <div class="rounded-xl border border-base-200 p-3 bg-base-50/50 space-y-2">
                            <div class="text-xs font-semibold text-base-content/60">{{ $answer->field->label }}</div>

                            @if($answer->field->type === 'file' && $answer->file_path)
                                <a href="{{ asset('storage/' . $answer->file_path) }}" target="_blank" class="link link-primary flex items-center gap-1">
                                    <x-app-icon icon="heroicons:document-arrow-down" class="h-4 w-4" />
                                    Скачать файл
                                </a>
                            @elseif($answer->field->type === 'url' && $answer->value_text)
                                <a href="{{ $answer->value_text }}" target="_blank" class="link link-primary break-all">
                                    {{ $answer->value_text }}
                                </a>
                            @else
                                <div class="whitespace-pre-wrap text-sm">{{ $answer->value_text ?? '—' }}</div>
                            @endif

                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text text-[11px] text-base-content/60">Комментарий судьи к этому полю</span>
                                </label>
                                <textarea
                                    class="textarea textarea-bordered textarea-sm"
                                    rows="2"
                                    wire:model.blur="fieldComments.{{ $answer->hackaton_case_field_id }}"
                                    wire:change="saveDraft"
                                    placeholder="Замечания по полю (автосохранение)">
                                </textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="card-title text-lg">Оценка</h2>
                        <div class="flex items-center gap-2 text-xs text-base-content/60">
                            <span x-show="$wire.isSaving" class="loading loading-spinner loading-xs"></span>
                            <span x-show="!$wire.isSaving">Ctrl+S — сохранить</span>
                        </div>
                    </div>

                    @php
                        $domain = $this->judgeDomain();
                        $rubric = is_array($submission->case->rubric_json) ? $submission->case->rubric_json : [];
                    @endphp

                    <div class="space-y-3">
                        @foreach($rubric as $criterion)
                            @php
                                $id = (string) ($criterion['id'] ?? '');
                                $label = (string) ($criterion['label'] ?? $id);
                                $max = (int) ($criterion['max'] ?? 0);
                                $cDomain = (string) ($criterion['domain'] ?? '');
                                $canEdit = $cDomain === $domain->value;
                            @endphp

                            @if($id !== '' && $max > 0)
                                <div class="rounded-xl border border-base-200 p-3 space-y-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-sm truncate">{{ $label }}</div>
                                            <div class="text-[11px] text-base-content/60">Макс: {{ $max }} • Домен: {{ $cDomain }}</div>
                                        </div>
                                        @if(!$canEdit)
                                            <span class="badge badge-ghost badge-sm">read-only</span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        <input
                                            type="number"
                                            min="0"
                                            max="{{ $max }}"
                                            @disabled(!$canEdit)
                                            class="input input-bordered input-sm w-24"
                                            wire:model.live.debounce.500ms="criteriaScores.{{ $id }}.score"
                                            wire:change="saveDraft"
                                        >

                                        <div class="flex flex-wrap gap-1">
                                            @foreach([5,7,8,9,10] as $quick)
                                                @if($quick <= $max)
                                                    <button
                                                        type="button"
                                                        class="btn btn-xs btn-outline"
                                                        @disabled(!$canEdit)
                                                        wire:click="$set('criteriaScores.{{ $id }}.score', {{ $quick }})"
                                                        wire:click.prevent="saveDraft"
                                                    >{{ $quick }}</button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <textarea
                                        class="textarea textarea-bordered textarea-sm w-full"
                                        rows="2"
                                        @disabled(!$canEdit)
                                        wire:model.blur="criteriaScores.{{ $id }}.comment"
                                        wire:change="saveDraft"
                                        placeholder="Комментарий к критерию (автосохранение)">
                                    </textarea>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text font-semibold">Общий комментарий</span>
                        </label>
                        <textarea
                            class="textarea textarea-bordered"
                            rows="3"
                            wire:model.blur="generalComment"
                            wire:change="saveDraft"
                            placeholder="Общее впечатление, рекомендации, ссылки, замечания..."></textarea>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <button class="btn btn-sm btn-outline" wire:click="saveDraft" wire:loading.attr="disabled">
                            Сохранить черновик
                        </button>
                        <button class="btn btn-sm btn-primary" wire:click="finalize" wire:loading.attr="disabled">
                            Финализировать
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="toast toast-end toast-top"
                x-data="{ open: false, message: '' }"
                x-on:toast.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 1500)"
            >
                <div class="alert alert-success" x-show="open">
                    <span x-text="message"></span>
                </div>
            </div>
        </div>
    </div>
</div>

