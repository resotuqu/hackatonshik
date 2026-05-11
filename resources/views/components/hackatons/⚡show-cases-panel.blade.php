<?php

use App\Actions\Hackaton\ResolveSubmitterTeamsForHackaton;
use App\Models\Hackaton;
use Livewire\Attributes\Lazy;
use Livewire\Component;

new #[Lazy] class extends Component
{
    public Hackaton $hackaton;

    public bool $isOrganizer = false;

    public bool $isAssignedJudge = false;

    /**
     * @var array<string, string>
     */
    public array $fieldTypeLabels = [];

    public function mount(
        Hackaton $hackaton,
        bool $isOrganizer,
        bool $isAssignedJudge,
        array $fieldTypeLabels,
    ): void {
        $this->authorize('view', $hackaton);
        $this->hackaton = $hackaton;
        $this->isOrganizer = $isOrganizer;
        $this->isAssignedJudge = $isAssignedJudge;
        $this->fieldTypeLabels = $fieldTypeLabels;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(ResolveSubmitterTeamsForHackaton $resolveSubmitterTeams)
    {
        $submitterTeams = auth()->check()
            ? $resolveSubmitterTeams->handle($this->hackaton)
            : collect();

        return view('components.hackatons.⚡show-cases-panel', [
            'submitterTeams' => $submitterTeams,
        ]);
    }
};
?>

<div class="space-y-4">
    <div class="card bg-base-100 border border-base-200 shadow-sm">
        <div class="card-body space-y-4">
            <h2 class="card-title text-xl">Кейсы</h2>

            @if($hackaton->cases->isEmpty())
                <x-empty-state
                    embedded
                    title="Кейсов пока нет"
                    description="Организатор ещё не опубликовал задания для этого хакатона."
                    icon="heroicons:puzzle-piece"
                />
            @else
                <div class="space-y-4">
                    @foreach($hackaton->cases as $case)
                        @if($case->isPublishedNow() || $isOrganizer)
                            <div class="rounded-xl border border-base-300 p-4 space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="flex-1 space-y-3">
                                        @if($case->images->isNotEmpty())
                                            <div class="max-w-md">
                                                <x-image-carousel
                                                    :carousel-id="'case-carousel-'.$case->id"
                                                    :items="$case->images"
                                                    aspect-class="aspect-video"
                                                    empty-text="Изображения кейса отсутствуют" />
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-xl font-bold">{{ $case->title }}</h3>
                                            <div class="markdown-body">
                                                {!! \App\Support\SafeMarkdown::toHtml($case->description ?? 'Описание отсутствует.') !!}
                                            </div>
                                            <p class="text-xs text-base-content/50 mt-2">
                                                Публикация: {{ $case->publish_at?->format('d.m.Y H:i') ?? 'сразу' }} |
                                                Дедлайн: {{ $case->deadline_at?->format('d.m.Y H:i') ?? 'не задан' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($isOrganizer)
                                        <div class="flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('hackatons.cases.destroy', [$hackaton, $case]) }}"
                                                onsubmit="return confirm('Удалить кейс?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-xs btn-error">Удалить кейс</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                @auth
                                    @php
                                        $myJoinedTeams = $submitterTeams->where('hackaton_case_id', $case->id);
                                        $canJoinAnyTeam = $submitterTeams->whereNull('hackaton_case_id')->isNotEmpty();
                                        $joinedAnyTeam = $submitterTeams->where('hackaton_case_id', $case->id)->isNotEmpty();
                                    @endphp

                                    @if($joinedAnyTeam && !empty($case->resources_json))
                                        <div class="rounded-xl bg-info/10 border border-info/20 p-4 space-y-2">
                                            <h4 class="font-semibold text-info flex items-center gap-2">
                                                <x-app-icon icon="heroicons:link" class="h-4 w-4" />
                                                Полезные ссылки для участников
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                @foreach($case->resources_json as $resource)
                                                    <a href="{{ $resource['url'] }}" target="_blank" class="flex items-center gap-2 p-2 rounded-lg bg-base-100 border border-info/10 hover:border-info/30 transition-colors">
                                                        <x-app-icon icon="heroicons:chat-bubble-left-right" class="h-4 w-4 text-info" />
                                                        <span class="text-sm font-medium">{{ $resource['label'] ?? 'Ссылка на чат/ресурс' }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($canJoinAnyTeam && !$isOrganizer)
                                        <div class="rounded-xl border border-primary/20 bg-primary/5 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                                            <div>
                                                <h4 class="font-semibold text-primary">Присоединиться к кейсу</h4>
                                                <p class="text-xs text-base-content/70">Выберите вашу одобренную команду, чтобы начать работу над этим кейсом.</p>
                                            </div>
                                            <form method="POST" action="{{ route('hackatons.cases.join', [$hackaton, $case]) }}" class="flex items-center gap-2 w-full md:w-auto">
                                                @csrf
                                                <select name="team_id" class="select select-bordered select-sm flex-1 md:w-48" required>
                                                    <option value="">Выберите команду</option>
                                                    @foreach($submitterTeams->whereNull('hackaton_case_id') as $team)
                                                        <option value="{{ $team->id }}">{{ $team->title }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-sm btn-primary">Вступить</button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth

                                @if($case->fields->isNotEmpty())
                                    <div class="collapse collapse-arrow border border-base-200 bg-base-100 rounded-xl overflow-hidden">
                                        <input type="checkbox" />
                                        <div class="collapse-title text-sm font-medium py-2 min-h-0">
                                            Требования к решению (поля)
                                        </div>
                                        <div class="collapse-content px-0">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Поле</th>
                                                        <th>Тип</th>
                                                        <th>Обязательное</th>
                                                        @if($isOrganizer)
                                                            <th></th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($case->fields as $field)
                                                        <tr>
                                                            <td>{{ $field->label }}</td>
                                                            <td>{{ $fieldTypeLabels[$field->type] ?? $field->type }}</td>
                                                            <td>{{ $field->is_required ? 'Да' : 'Нет' }}</td>
                                                            @if($isOrganizer)
                                                                <td class="text-right">
                                                                    <form method="POST" action="{{ route('hackatons.cases.fields.destroy', [$hackaton, $case, $field]) }}"
                                                                        onsubmit="return confirm('Удалить поле?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="btn btn-xs btn-ghost text-error">Удалить</button>
                                                                    </form>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                @auth
                                    @if(!$isOrganizer && $case->isOpenForSubmission() && $case->fields->isNotEmpty())
                                        @php
                                            $isInDevStatus = $hackaton->status === \App\Enums\HackatonStatus::IN_PROGRESS;
                                        @endphp

                                        @if($submitterTeams->where('hackaton_case_id', $case->id)->isNotEmpty())
                                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                                                @php
                                                    $personalSubmission = $case->submissions->where('user_id', auth()->id())->where('team_id', null)->first();
                                                    $personalAnswers = $personalSubmission ? $personalSubmission->answers->keyBy('hackaton_case_field_id') : collect();
                                                @endphp
                                                <form method="POST" enctype="multipart/form-data"
                                                    action="{{ route('hackatons.cases.submissions.store', [$hackaton, $case]) }}"
                                                    class="space-y-3 rounded-xl border border-base-300 p-4 bg-base-100 shadow-sm">
                                                    @csrf
                                                    <div class="flex items-center justify-between border-b pb-2">
                                                        <h4 class="font-bold text-lg">Личное решение</h4>
                                                        @if($personalSubmission)
                                                            <span class="badge badge-success">Отправлено</span>
                                                        @endif
                                                    </div>
                                                    <input type="hidden" name="scope" value="user">
                                                    <div class="space-y-3">
                                                        @foreach($case->fields as $field)
                                                            @php
                                                                $ans = $personalAnswers->get($field->id);
                                                                $isUrlField = $field->type === 'url';
                                                                $isBlockedUrl = $isUrlField && !$isInDevStatus;
                                                            @endphp

                                                            <div class="form-control">
                                                                <label class="label py-1">
                                                                    <span class="label-text font-semibold">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                </label>

                                                                @if($field->type === 'file')
                                                                    <input type="file" name="files[{{ $field->id }}]" class="file-input file-input-bordered w-full file-input-sm">
                                                                    @if($ans && $ans->file_path)
                                                                        <div class="mt-1 flex items-center gap-2 text-xs text-success">
                                                                            <x-app-icon icon="heroicons:check-circle" class="h-4 w-4" />
                                                                            <span>Файл загружен: <a href="{{ asset('storage/' . $ans->file_path) }}" target="_blank" class="link">Посмотреть</a></span>
                                                                        </div>
                                                                    @endif
                                                                @elseif($field->type === 'textarea')
                                                                    <textarea name="answers[{{ $field->id }}]" rows="3" class="textarea textarea-bordered w-full textarea-sm">{{ old("answers.{$field->id}", $ans?->value_text) }}</textarea>
                                                                @else
                                                                    <div class="relative">
                                                                        <input
                                                                            type="{{ $isUrlField ? 'url' : 'text' }}"
                                                                            name="answers[{{ $field->id }}]"
                                                                            value="{{ old("answers.{$field->id}", $ans?->value_text) }}"
                                                                            @disabled($isBlockedUrl)
                                                                            class="input input-bordered w-full input-sm @if($isBlockedUrl) bg-base-200 @endif"
                                                                            @if($isUrlField) placeholder="https://..." @endif>
                                                                        @if($isBlockedUrl)
                                                                            <div class="mt-1 flex items-center gap-1 text-[10px] text-warning uppercase font-bold tracking-wider">
                                                                                <x-app-icon icon="heroicons:lock-closed" class="h-3 w-3" />
                                                                                Будет доступно в статусе «В процессе»
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button class="btn btn-sm btn-primary w-full shadow-md">
                                                        {{ $personalSubmission ? 'Обновить решение' : 'Отправить решение' }}
                                                    </button>
                                                </form>

                                                <div class="space-y-4">
                                                    @foreach($submitterTeams->where('hackaton_case_id', $case->id) as $team)
                                                        @php
                                                            $teamSubmission = $case->submissions->where('team_id', $team->id)->first();
                                                            $teamAnswers = $teamSubmission ? $teamSubmission->answers->keyBy('hackaton_case_field_id') : collect();
                                                        @endphp
                                                        <form method="POST" enctype="multipart/form-data"
                                                            action="{{ route('hackatons.cases.submissions.store', [$hackaton, $case]) }}"
                                                            class="space-y-3 rounded-xl border border-base-300 p-4 bg-base-50/50">
                                                            @csrf
                                                            <div class="flex items-center justify-between border-b border-base-200 pb-2">
                                                                <h4 class="font-bold">От имени команды «{{ $team->title }}»</h4>
                                                                @if($teamSubmission)
                                                                    <span class="badge badge-success badge-sm">Отправлено</span>
                                                                @endif
                                                            </div>
                                                            <input type="hidden" name="scope" value="team">
                                                            <input type="hidden" name="team_id" value="{{ $team->id }}">

                                                            <div class="space-y-3">
                                                                @foreach($case->fields as $field)
                                                                    @php
                                                                        $ans = $teamAnswers->get($field->id);
                                                                        $isUrlField = $field->type === 'url';
                                                                        $isBlockedUrl = $isUrlField && !$isInDevStatus;
                                                                    @endphp
                                                                    <div class="form-control">
                                                                        <label class="label py-1">
                                                                            <span class="label-text text-xs font-semibold">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                        </label>
                                                                        @if($field->type === 'file')
                                                                            <input type="file" name="files[{{ $field->id }}]" class="file-input file-input-bordered file-input-sm w-full">
                                                                            @if($ans && $ans->file_path)
                                                                                <div class="mt-1 flex items-center gap-1 text-[10px] text-success">
                                                                                    <x-app-icon icon="heroicons:check" class="h-3 w-3" />
                                                                                    <span>Файл есть: <a href="{{ asset('storage/' . $ans->file_path) }}" target="_blank" class="link">Просмотр</a></span>
                                                                                </div>
                                                                            @endif
                                                                        @elseif($field->type === 'textarea')
                                                                            <textarea name="answers[{{ $field->id }}]" rows="2" class="textarea textarea-bordered textarea-sm w-full">{{ old("answers.{$field->id}", $ans?->value_text) }}</textarea>
                                                                        @else
                                                                            <div class="relative">
                                                                                <input
                                                                                    type="{{ $isUrlField ? 'url' : 'text' }}"
                                                                                    name="answers[{{ $field->id }}]"
                                                                                    value="{{ old("answers.{$field->id}", $ans?->value_text) }}"
                                                                                    @disabled($isBlockedUrl)
                                                                                    class="input input-bordered input-sm w-full @if($isBlockedUrl) bg-base-200 @endif">
                                                                                @if($isBlockedUrl)
                                                                                    <div class="mt-1 text-[9px] text-warning font-bold">ТОЛЬКО В СТАТУСЕ «В ПРОЦЕССЕ»</div>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button class="btn btn-xs btn-primary w-full mt-2">
                                                                {{ $teamSubmission ? 'Обновить командное решение' : 'Отправить командное решение' }}
                                                            </button>
                                                        </form>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info shadow-sm">
                                                <x-app-icon icon="heroicons:information-circle" class="h-5 w-5" />
                                                <div class="flex flex-col">
                                                    <span class="font-bold">Вы не выбрали этот кейс</span>
                                                    <span class="text-xs">Для отправки решения ваша команда должна сначала «Присоединиться» к этому кейсу выше.</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endauth

                                @if(($isOrganizer || $isAssignedJudge) && $case->submissions->isNotEmpty())
                                    <div class="rounded-xl border border-base-300 p-3 space-y-3">
                                        <p class="font-medium">Оценивание решений</p>
                                        @foreach($case->submissions as $submission)
                                            <div class="rounded-lg border border-base-200 p-3 bg-base-50/50">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="font-medium text-sm">
                                                        {{ $submission->team?->title ?? ($submission->user?->nickname ?? $submission->user?->email ?? 'Личное решение') }}
                                                    </span>
                                                    <span class="text-xs text-base-content/50">
                                                        {{ $submission->submitted_at?->format('d.m.Y H:i') }}
                                                    </span>
                                                </div>

                                                @if($submission->answers->isNotEmpty())
                                                    <div class="space-y-2 mb-4">
                                                        @foreach($submission->answers as $answer)
                                                            <div class="text-sm">
                                                                <p class="text-xs font-semibold text-base-content/60">{{ $answer->field->label }}:</p>
                                                                @if($answer->field->type === 'file' && $answer->file_path)
                                                                    <a href="{{ asset('storage/' . $answer->file_path) }}" target="_blank" class="link link-primary flex items-center gap-1 mt-1">
                                                                        <x-app-icon icon="heroicons:document-arrow-down" class="h-4 w-4" />
                                                                        Скачать файл
                                                                    </a>
                                                                @elseif($answer->field->type === 'url' && $answer->value_text)
                                                                    <a href="{{ $answer->value_text }}" target="_blank" class="link link-primary break-all">
                                                                        {{ $answer->value_text }}
                                                                    </a>
                                                                @else
                                                                    <p class="mt-1 whitespace-pre-wrap">{{ $answer->value_text ?? '—' }}</p>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <form method="POST" action="{{ route('hackatons.scores.store', $hackaton) }}" class="grid grid-cols-1 md:grid-cols-4 gap-2">
                                                    @csrf
                                                    <input type="hidden" name="hackaton_case_submission_id" value="{{ $submission->id }}">
                                                    <div class="md:col-span-2 flex items-center gap-2">
                                                        <input name="score" type="number" min="0" max="100" class="input input-bordered input-sm w-full"
                                                            placeholder="Балл"
                                                            value="{{ $submission->score?->score }}">
                                                        <span class="text-base-content/50">/</span>
                                                        <input name="max_score" type="number" min="1" max="100" class="input input-bordered input-sm w-full"
                                                            placeholder="Макс"
                                                            value="{{ $submission->score?->max_score ?? 100 }}">
                                                    </div>
                                                    <button class="btn btn-sm btn-primary md:col-span-2">Сохранить оценку</button>
                                                    <textarea name="comment" rows="1" class="textarea textarea-bordered textarea-sm md:col-span-4"
                                                        placeholder="Комментарий к оценке">{{ $submission->score?->comment }}</textarea>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($isOrganizer)
                                    <div class="divider my-1"></div>
                                    <x-organizer-action-modal
                                        :modal-id="'organizer-case-field-create-modal-'.$case->id"
                                        button-label="Добавить поле"
                                        button-class="btn btn-sm btn-outline"
                                        title="Добавить поле к кейсу"
                                        :description="'Кейс: '.$case->title">
                                        <form method="POST" action="{{ route('hackatons.cases.fields.store', [$hackaton, $case]) }}"
                                            class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                            @csrf
                                            <input type="hidden" name="_open_modal" value="{{ 'organizer-case-field-create-modal-'.$case->id }}">
                                            <input name="label" class="input input-bordered" placeholder="Название поля" required autofocus>
                                            <select name="type" class="select select-bordered" required>
                                                <option value="text">Короткий текст</option>
                                                <option value="url">Ссылка</option>
                                                <option value="textarea">Большой текст</option>
                                                <option value="file">Файл</option>
                                            </select>
                                            <label class="label cursor-pointer justify-start gap-2">
                                                <input type="checkbox" name="is_required" value="1" class="checkbox checkbox-sm">
                                                <span class="label-text">Обязательное поле</span>
                                            </label>
                                            <button class="btn btn-sm btn-outline md:col-span-2">Добавить поле</button>
                                        </form>
                                    </x-organizer-action-modal>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @guest
        <div class="alert">
            <span>Чтобы подать заявку на участие команды, <a class="link link-primary" href="/login">войдите в аккаунт</a>.</span>
        </div>
    @endguest
</div>