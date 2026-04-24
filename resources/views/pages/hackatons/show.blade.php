@extends('layouts.app')

@section('title', $hackaton->title)

@section('slot')
    @php
        $hackatonImage = filled($hackaton->image_url)
            ? (str_starts_with($hackaton->image_url, 'http') ? $hackaton->image_url : asset('storage/' . $hackaton->image_url))
            : null;
        $fieldTypeLabels = [
            'text' => 'Короткий текст',
            'url' => 'Ссылка',
            'textarea' => 'Большой текст',
            'file' => 'Файл',
        ];
        $teamsCount = $hackaton->teams->count();
        $participantsCount = $hackaton->teams->sum(fn($team) => $team->roles->whereNotNull('user_id')->count());
        $myTeamIds = auth()->check() ? $availableTeams->pluck('id') : collect();
        $myApplicationsByTeam = auth()->check()
            ? $hackaton->applications->whereIn('team_id', $myTeamIds)->keyBy('team_id')
            : collect();
        $teamsWithoutApplication = auth()->check()
            ? $availableTeams->reject(fn ($team) => $myApplicationsByTeam->has($team->id))->values()
            : collect();
        $isOrganizer = auth()->check() && $hackaton->user_id === auth()->id();
        $announcementTemplates = [
            'start' => 'Старт хакатона',
            'deadline' => 'Напоминание о дедлайне',
            'results' => 'Публикация результатов',
        ];
        $issuedCertificatesByUser = $hackaton->certificates->groupBy('user_id');
        $nextStepTitle = 'Авторизуйтесь';
        $nextStepHint = 'Войдите в аккаунт, чтобы подавать заявки и отправлять решения кейсов.';

        if (auth()->check()) {
            if ($isOrganizer) {
                $nextStepTitle = 'Управляйте хакатоном';
                $nextStepHint = 'Публикуйте анонсы и кейсы, а затем рассматривайте заявки команд.';
            } elseif ($availableTeams->isEmpty()) {
                $nextStepTitle = 'Создайте команду';
                $nextStepHint = 'Без команды нельзя подать заявку на участие в хакатоне.';
            } elseif ($teamsWithoutApplication->isNotEmpty()) {
                $nextStepTitle = 'Подайте заявку команды';
                $nextStepHint = 'Выберите команду и отправьте заявку на участие прямо на этой странице.';
            } elseif ($myApplicationsByTeam->where('status', \App\Enums\ApplicationStatus::accepted())->isNotEmpty()) {
                $nextStepTitle = 'Отправьте решение кейса';
                $nextStepHint = 'Команда допущена: перейдите к блоку кейсов и отправьте ответы.';
            } else {
                $nextStepTitle = 'Ожидайте модерацию';
                $nextStepHint = 'Заявка уже отправлена. Следите за обновлением статуса ниже.';
            }
        }
    @endphp

    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="/hackatons">Хакатоны</a></li>
                <li class="opacity-70">{{ $hackaton->title }}</li>
            </ul>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card bg-base-100 border border-base-200 shadow-sm">
                <figure class="aspect-video bg-base-200">
                    @if ($hackatonImage)
                        <img src="{{ $hackatonImage }}" class="h-full w-full object-cover" alt="{{ $hackaton->title }}">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-base-content/60">Изображение хакатона отсутствует</div>
                    @endif
                </figure>
                <div class="card-body">
                    <h1 class="card-title text-3xl">{{ $hackaton->title }}</h1>
                    <div class="prose max-w-none prose-sm sm:prose-base">
                        {!! \Illuminate\Support\Str::markdown($hackaton->description ?? 'Описание отсутствует.') !!}
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body space-y-3">
                    <h2 class="card-title text-lg">Информация о хакатоне</h2>
                    <div class="alert alert-info items-start">
                        <div>
                            <p class="font-semibold">Ваш следующий шаг: {{ $nextStepTitle }}</p>
                            <p class="text-sm">{{ $nextStepHint }}</p>
                        </div>
                    </div>
                    <p class="text-sm">Организатор: <span class="font-medium">{{ $hackaton->user->nickname ?? $hackaton->user->name ?? $hackaton->user->email }}</span></p>
                    <p class="text-sm">Начало: <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($hackaton->start_at)->format('d.m.Y H:i') }}</span></p>
                    <p class="text-sm">Конец: <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($hackaton->end_at)->format('d.m.Y H:i') }}</span></p>
                    <p class="text-sm">Команд: <span class="font-medium">{{ $teamsCount }}</span></p>
                    <p class="text-sm">Участников: <span class="font-medium">{{ $participantsCount }}</span></p>

                    @auth
                        @if ($hackaton->user_id !== auth()->id())
                            <div class="divider my-1"></div>
                            @if ($myApplicationsByTeam->isNotEmpty())
                                <div class="space-y-2">
                                    <p class="text-sm font-medium">Ваши заявки</p>
                                    @foreach ($myApplicationsByTeam as $myApplication)
                                        <div class="rounded-xl border border-base-300 p-2 text-sm">
                                            <p>
                                                Команда:
                                                <span class="font-medium">{{ $myApplication->team->title }}</span>
                                            </p>
                                            <div class="mt-1 flex items-center justify-between gap-2">
                                                <span class="badge badge-{{ $myApplication->status->isAccepted() ? 'success' : ($myApplication->status->isRejected() ? 'error' : 'warning') }}">
                                                    {{ $myApplication->status->label() }}
                                                </span>
                                                @if ($myApplication->status->isPending())
                                                    <form method="POST" action="{{ route('hackaton.applications.destroy', $myApplication) }}"
                                                        onsubmit="return confirm('Отменить поданную заявку команды?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-xs btn-ghost">Отменить</button>
                                                    </form>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs text-base-content/70">
                                                @if ($myApplication->status->isAccepted())
                                                    Команда допущена. Переходите к блоку «Кейсы» и отправляйте решение.
                                                @elseif ($myApplication->status->isRejected())
                                                    Заявка отклонена. Проверьте требования хакатона и подайте новую заявку другой командой.
                                                @else
                                                    Заявка на рассмотрении. Мы уведомим вас после решения организатора.
                                                @endif
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($teamsWithoutApplication->isNotEmpty())
                                <x-application-modal type="hackaton" :id="$hackaton->id" :teams="$teamsWithoutApplication"
                                    title="Подать заявку команды на хакатон"
                                    action="{{ route('hackaton.applications.store') }}" />
                            @elseif ($availableTeams->isNotEmpty())
                                <p class="text-sm text-base-content/70">
                                    Все ваши команды уже подали заявки на этот хакатон.
                                </p>
                            @else
                                <p class="text-sm text-base-content/70">
                                    У вас пока нет команд для подачи заявки.
                                </p>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <div class="divider">Для участников</div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-xl">Документы хакатона</h2>
                @if ($hackaton->documents->isEmpty())
                    <p class="text-base-content/60">Для этого хакатона пока нет документов. Проверяйте раздел позже или уточните детали у организатора.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($hackaton->documents as $document)
                            <div class="rounded-xl border border-base-300 p-4">
                                <p class="font-semibold">{{ $document->name }}</p>
                                <p class="text-sm text-base-content/70 mt-1">{{ $document->description }}</p>
                                <div class="mt-3">
                                    <a class="btn btn-sm btn-outline"
                                        href="{{ asset('storage/' . $document->file_url) }}"
                                        target="_blank"
                                        rel="noopener noreferrer">
                                        Открыть документ
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="card-title text-xl">Анонсы</h2>
                </div>

                @if($hackaton->announcements->isEmpty())
                    <p class="text-base-content/60">Пока нет опубликованных анонсов. Проверяйте раздел перед стартом и во время хакатона.</p>
                @else
                    <div class="space-y-3">
                        @foreach($hackaton->announcements as $announcement)
                            <div class="rounded-xl border border-base-300 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <p class="font-semibold">{{ $announcement->title }}</p>
                                        <p class="text-xs text-base-content/70">
                                            {{ $announcement->published_at?->format('d.m.Y H:i') ?? '—' }}
                                        </p>
                                        @if($isOrganizer && $announcement->is_draft)
                                            <span class="badge badge-warning badge-xs">Черновик</span>
                                        @elseif($isOrganizer && $announcement->published_at?->isFuture())
                                            <span class="badge badge-info badge-xs">Запланировано</span>
                                        @endif
                                    </div>
                                    @if($isOrganizer)
                                        <form method="POST" action="{{ route('hackatons.announcements.destroy', [$hackaton, $announcement]) }}"
                                            onsubmit="return confirm('Удалить анонс?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-xs btn-error">Удалить</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="prose max-w-none prose-sm mt-2">
                                    {!! \Illuminate\Support\Str::markdown($announcement->body) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($isOrganizer)
                    <div class="divider"></div>
                    <form method="POST" action="{{ route('hackatons.announcements.store', $hackaton) }}" class="space-y-3">
                        @csrf
                        <h3 class="font-semibold">Опубликовать анонс</h3>
                        <input name="title" class="input input-bordered w-full" placeholder="Заголовок анонса" required>
                        <textarea name="body" class="textarea textarea-bordered w-full" rows="4" placeholder="Текст анонса" required></textarea>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <select name="template_key" class="select select-bordered">
                                <option value="">Без шаблона</option>
                                @foreach($announcementTemplates as $templateKey => $templateName)
                                    <option value="{{ $templateKey }}">{{ $templateName }}</option>
                                @endforeach
                            </select>
                            <input name="published_at" type="datetime-local" class="input input-bordered">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="checkbox" name="is_draft" value="1" class="checkbox checkbox-sm">
                                <span class="label-text">Сохранить как черновик</span>
                            </label>
                        </div>
                        <button class="btn btn-primary btn-sm">Сохранить анонс</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body space-y-4">
                <h2 class="card-title text-xl">Кейсы</h2>

                @if($hackaton->cases->isEmpty())
                    <p class="text-base-content/60">Для этого хакатона пока нет кейсов.</p>
                @else
                    <div class="space-y-4">
                        @foreach($hackaton->cases as $case)
                            @if($case->isPublishedNow() || $isOrganizer)
                                <div class="rounded-xl border border-base-300 p-4 space-y-3">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p class="font-semibold">{{ $case->title }}</p>
                                            <p class="text-sm text-base-content/70">{{ $case->description }}</p>
                                            <p class="text-xs text-base-content/70 mt-1">
                                                Публикация: {{ $case->publish_at?->format('d.m.Y H:i') ?? 'сразу' }} |
                                                Дедлайн: {{ $case->deadline_at?->format('d.m.Y H:i') ?? 'не задан' }}
                                            </p>
                                        </div>
                                        @if($isOrganizer)
                                            <form method="POST" action="{{ route('hackatons.cases.destroy', [$hackaton, $case]) }}"
                                                onsubmit="return confirm('Удалить кейс?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-xs btn-error">Удалить кейс</button>
                                            </form>
                                        @endif
                                    </div>

                                    @if($case->fields->isNotEmpty())
                                        <div class="overflow-x-auto">
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
                                    @endif

                                    @auth
                                        @if(!$isOrganizer && $case->isOpenForSubmission() && $case->fields->isNotEmpty())
                                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                                                @if($submitterTeams->isNotEmpty())
                                                    <form method="POST" enctype="multipart/form-data"
                                                        action="{{ route('hackatons.cases.submissions.store', [$hackaton, $case]) }}"
                                                        class="space-y-2 rounded-xl border border-base-300 p-3">
                                                        @csrf
                                                        <h4 class="font-medium">Отправить от команды</h4>
                                                        <p class="text-xs text-base-content/70">Заполняйте поля внимательно: обязательные отмечены звездочкой (*).</p>
                                                        <input type="hidden" name="scope" value="team">
                                                        <select name="team_id" class="select select-bordered w-full" required>
                                                            <option value="">Выберите команду</option>
                                                            @foreach($submitterTeams as $team)
                                                                <option value="{{ $team->id }}">{{ $team->title }}</option>
                                                            @endforeach
                                                        </select>
                                                        @foreach($case->fields as $field)
                                                            @if($field->type === 'file')
                                                                <label class="form-control">
                                                                    <span class="label-text mb-1">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                    <input type="file" name="files[{{ $field->id }}]" class="file-input file-input-bordered w-full">
                                                                </label>
                                                            @elseif($field->type === 'textarea')
                                                                <label class="form-control">
                                                                    <span class="label-text mb-1">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                    <textarea name="answers[{{ $field->id }}]" rows="3" class="textarea textarea-bordered w-full"></textarea>
                                                                </label>
                                                            @else
                                                                <label class="form-control">
                                                                    <span class="label-text mb-1">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                    <input
                                                                        type="{{ $field->type === 'url' ? 'url' : 'text' }}"
                                                                        name="answers[{{ $field->id }}]"
                                                                        class="input input-bordered w-full">
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                        <button class="btn btn-sm btn-primary">Отправить решение от команды</button>
                                                    </form>
                                                @endif

                                                <form method="POST" enctype="multipart/form-data"
                                                    action="{{ route('hackatons.cases.submissions.store', [$hackaton, $case]) }}"
                                                    class="space-y-2 rounded-xl border border-base-300 p-3">
                                                    @csrf
                                                    <h4 class="font-medium">Отправить лично</h4>
                                                    <p class="text-xs text-base-content/70">Заполняйте поля внимательно: обязательные отмечены звездочкой (*).</p>
                                                    <input type="hidden" name="scope" value="user">
                                                    @foreach($case->fields as $field)
                                                        @if($field->type === 'file')
                                                            <label class="form-control">
                                                                <span class="label-text mb-1">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                <input type="file" name="files[{{ $field->id }}]" class="file-input file-input-bordered w-full">
                                                            </label>
                                                        @elseif($field->type === 'textarea')
                                                            <label class="form-control">
                                                                <span class="label-text mb-1">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                <textarea name="answers[{{ $field->id }}]" rows="3" class="textarea textarea-bordered w-full"></textarea>
                                                            </label>
                                                        @else
                                                            <label class="form-control">
                                                                <span class="label-text mb-1">{{ $field->label }} @if($field->is_required)*@endif</span>
                                                                <input
                                                                    type="{{ $field->type === 'url' ? 'url' : 'text' }}"
                                                                    name="answers[{{ $field->id }}]"
                                                                    class="input input-bordered w-full">
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                    <button class="btn btn-sm btn-primary">Отправить личное решение</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth

                                    @if($isOrganizer && $case->submissions->isNotEmpty())
                                        <div class="rounded-xl border border-base-300 p-3 space-y-3">
                                            <p class="font-medium">Оценивание решений</p>
                                            @foreach($case->submissions as $submission)
                                                <form method="POST" action="{{ route('hackatons.scores.store', $hackaton) }}" class="grid grid-cols-1 md:grid-cols-5 gap-2">
                                                    @csrf
                                                    <input type="hidden" name="hackaton_case_submission_id" value="{{ $submission->id }}">
                                                    <input class="input input-bordered md:col-span-2"
                                                        value="{{ $submission->team?->title ?? ($submission->user?->email ?? 'Личное решение') }}"
                                                        readonly>
                                                    <input name="score" type="number" min="0" max="100" class="input input-bordered"
                                                        value="{{ $submission->score?->score }}">
                                                    <input name="max_score" type="number" min="1" max="100" class="input input-bordered"
                                                        value="{{ $submission->score?->max_score ?? 100 }}">
                                                    <button class="btn btn-sm btn-outline">Сохранить</button>
                                                    <textarea name="comment" rows="2" class="textarea textarea-bordered md:col-span-5"
                                                        placeholder="Комментарий к оценке">{{ $submission->score?->comment }}</textarea>
                                                </form>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($isOrganizer)
                                        <div class="divider my-1"></div>
                                        <form method="POST" action="{{ route('hackatons.cases.fields.store', [$hackaton, $case]) }}"
                                            class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            @csrf
                                            <input name="label" class="input input-bordered" placeholder="Название поля" required>
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

        @if($isOrganizer)
            <div class="divider">Для организатора</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <p class="text-sm text-base-content/70">Заявки (всего)</p>
                        <p class="text-3xl font-semibold">{{ $metrics['applications_total'] }}</p>
                        <p class="text-xs">В работе: {{ $metrics['applications_pending'] }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <p class="text-sm text-base-content/70">Решения кейсов</p>
                        <p class="text-3xl font-semibold">{{ $metrics['submissions_total'] }}</p>
                        <p class="text-xs">Оценено: {{ $metrics['submissions_scored'] }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 border border-base-200 shadow-sm">
                    <div class="card-body">
                        <p class="text-sm text-base-content/70">Прогресс оценивания</p>
                        <p class="text-3xl font-semibold">{{ $metrics['submissions_scored_percent'] }}%</p>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-xl">Рейтинг команд</h2>
                    @if($leaderboard->isEmpty())
                        <p class="text-base-content/60">Пока нет оцененных решений команд.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Команда</th>
                                        <th>Баллы</th>
                                        <th>Макс. баллы</th>
                                        <th>Прогресс</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaderboard as $row)
                                        <tr>
                                            <td>{{ $row['team']->title }}</td>
                                            <td>{{ $row['total_score'] }}</td>
                                            <td>{{ $row['max_score'] }}</td>
                                            <td>{{ $row['progress_percent'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body space-y-4">
                    <h2 class="card-title text-xl">Управление кейсами</h2>

                    <form method="POST" action="{{ route('hackatons.cases.store', $hackaton) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @csrf
                        <input name="title" class="input input-bordered" placeholder="Название кейса" required>
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="is_published" value="1" class="checkbox checkbox-sm">
                            <span class="label-text">Опубликовать сразу</span>
                        </label>
                        <input name="publish_at" type="datetime-local" class="input input-bordered" placeholder="Дата публикации">
                        <input name="deadline_at" type="datetime-local" class="input input-bordered" placeholder="Дедлайн">
                        <textarea name="description" rows="3" class="textarea textarea-bordered md:col-span-2"
                            placeholder="Описание кейса"></textarea>
                        <button class="btn btn-primary btn-sm md:col-span-2">Добавить кейс</button>
                    </form>
                </div>
            </div>

            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body space-y-4">
                    <h2 class="card-title text-xl">Сертификаты участников</h2>
                    @if($participantUsers->isEmpty())
                        <p class="text-base-content/60">Пока нет участников для выдачи сертификатов.</p>
                    @else
                        <form method="POST" enctype="multipart/form-data" action="{{ route('hackatons.certificates.store', $hackaton) }}"
                            class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @csrf
                            <select name="user_ids[]" class="select select-bordered" multiple required>
                                @foreach($participantUsers as $participant)
                                    <option value="{{ $participant->id }}">
                                        {{ $participant->fio ?? $participant->nickname ?? $participant->email }}
                                        @if($issuedCertificatesByUser->has($participant->id))
                                            (уже выдано: {{ $issuedCertificatesByUser->get($participant->id)->count() }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <input name="title" class="input input-bordered" placeholder="Название сертификата" required>
                            <input name="issued_at" type="date" class="input input-bordered">
                            <input name="file" type="file" class="file-input file-input-bordered" required>
                            <button class="btn btn-primary btn-sm md:col-span-2">Загрузить сертификат</button>
                        </form>
                    @endif

                    @if($hackaton->certificates->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Участник</th>
                                        <th>Название</th>
                                        <th>Дата выдачи</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hackaton->certificates as $certificate)
                                        <tr>
                                            <td>{{ $certificate->user->fio ?? $certificate->user->nickname ?? $certificate->user->email }}</td>
                                            <td>{{ $certificate->title }}</td>
                                            <td>{{ $certificate->issued_at?->format('d.m.Y') ?? '—' }}</td>
                                            <td class="text-right">
                                                <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-xs btn-outline">Скачать</a>
                                                <form method="POST" action="{{ route('hackatons.certificates.destroy', [$hackaton, $certificate]) }}" class="inline-block"
                                                    onsubmit="return confirm('Удалить сертификат?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-xs btn-ghost text-error">Удалить</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($isOrganizer)
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-xl">Заявки команд</h2>
                    <form method="GET" class="my-3 flex items-center gap-2">
                        <select name="applications_status" class="select select-bordered select-sm">
                            <option value="">Все статусы</option>
                            <option value="pending" @selected($applicationStatusFilter === 'pending')>На рассмотрении</option>
                            <option value="accepted" @selected($applicationStatusFilter === 'accepted')>Принята</option>
                            <option value="rejected" @selected($applicationStatusFilter === 'rejected')>Отклонена</option>
                        </select>
                        <button class="btn btn-sm btn-outline">Фильтровать</button>
                    </form>

                    @if($applications->isEmpty())
                    <p class="text-base-content/60">Пока нет заявок. Когда команды подадут заявки, они появятся в этом списке.</p>
                    @else
                        <div class="mb-3 flex items-center gap-2">
                            <select form="bulk-status-update" name="status" class="select select-bordered select-sm">
                                <option value="accepted">Принять</option>
                                <option value="rejected">Отклонить</option>
                            </select>
                            <button form="bulk-status-update" class="btn btn-sm btn-primary">Применить к выбранным</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Команда</th>
                                        <th>Сообщение</th>
                                        <th>Отправлена</th>
                                        <th>Статус</th>
                                        <th>Рассмотрел</th>
                                        <th>Рассмотрена</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $app)
                                        <tr>
                                            <td>
                                                @if($app->status->isPending())
                                                    <input form="bulk-status-update" type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="checkbox checkbox-sm">
                                                @endif
                                            </td>
                                            <td>{{ $app->team->title }}</td>
                                            <td class="max-w-xs truncate">{{ $app->message }}</td>
                                            <td>{{ $app->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $app->status->isAccepted() ? 'success' : ($app->status->isRejected() ? 'error' : 'warning') }}">
                                                    {{ $app->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $app->reviewer?->nickname ?? $app->reviewer?->name ?? '—' }}</td>
                                            <td>{{ $app->reviewed_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                            <td>
                                                @if($app->status->isPending())
                                                    <form method="POST" action="{{ route('hackaton.applications.update', $app) }}" class="inline-flex">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button class="btn btn-success btn-xs">Принять</button>
                                                    </form>

                                                    <form method="POST" action="{{ route('hackaton.applications.update', $app) }}" class="inline-flex ml-2"
                                                        onsubmit="return confirm('Отклонить заявку команды?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button class="btn btn-error btn-xs">Отклонить</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form id="bulk-status-update" method="POST" action="{{ route('hackaton.applications.bulk-update', $hackaton) }}" class="hidden">
                            @csrf
                            @method('PATCH')
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection