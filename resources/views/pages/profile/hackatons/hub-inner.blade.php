    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="/profile">Профиль</a></li>
                <li><a href="/hackatons/{{ $hackaton->id }}">{{ $hackaton->title }}</a></li>
                <li class="opacity-70">Мой хакатон</li>
            </ul>
        </div>

        <section class="card border border-base-200 bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="card-title text-2xl">{{ $hackaton->title }}</h1>
                        <p class="text-sm text-base-content/70">Личный кабинет участника с ключевыми действиями и дедлайнами.</p>
                    </div>
                    <a href="{{ route('hackatons.show', $hackaton) }}" class="btn btn-outline btn-sm">Открыть страницу хакатона</a>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 mt-3">
                    <div class="rounded-xl border border-base-300 p-3">
                        <p class="text-xs text-base-content/70">Мои команды</p>
                        <p class="text-2xl font-semibold">{{ $teams->count() }}</p>
                    </div>
                    <div class="rounded-xl border border-base-300 p-3">
                        <p class="text-xs text-base-content/70">Мои заявки</p>
                        <p class="text-2xl font-semibold">{{ $applications->count() }}</p>
                    </div>
                    <div class="rounded-xl border border-base-300 p-3">
                        <p class="text-xs text-base-content/70">Отправки кейсов</p>
                        <p class="text-2xl font-semibold">{{ $submissions->count() }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <article class="card border border-base-200 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg">Статусы заявок</h2>
                    @if($applications->isEmpty())
                        <p class="text-sm text-base-content/70">Пока нет заявок команд на этот хакатон.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($applications as $application)
                                <div class="rounded-lg border border-base-300 p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-medium">{{ $application->team?->title }}</p>
                                        <span class="badge badge-{{ $application->status->isAccepted() ? 'success' : ($application->status->isRejected() ? 'error' : 'warning') }}">
                                            {{ $application->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-base-content/70 mt-1">
                                        Обновлено: {{ optional($application->updated_at)->format('d.m.Y H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>

            <article class="card border border-base-200 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg">Обязательные документы</h2>
                    @if($requiredDocuments->isEmpty())
                        <p class="text-sm text-base-content/70">Организатор пока не добавил обязательные документы.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($requiredDocuments as $document)
                                <div class="rounded-lg border border-base-300 p-3 flex items-center justify-between">
                                    <p class="font-medium">{{ $document->name }}</p>
                                    <span class="badge {{ $document->uploaded_count > 0 ? 'badge-success' : 'badge-warning' }}">
                                        {{ $document->uploaded_count > 0 ? 'Загружен' : 'Не загружен' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <article class="card border border-base-200 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg">Ближайшие дедлайны кейсов</h2>
                    @if($upcomingCases->isEmpty())
                        <p class="text-sm text-base-content/70">Активных дедлайнов пока нет.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($upcomingCases as $case)
                                <div class="rounded-lg border border-base-300 p-3 flex items-center justify-between">
                                    <p class="font-medium">{{ $case->title }}</p>
                                    <p class="text-sm">{{ \Illuminate\Support\Carbon::parse($case->deadline_at)->format('d.m.Y H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>

            <article class="card border border-base-200 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg">Последние отправки</h2>
                    @if($submissions->isEmpty())
                        <p class="text-sm text-base-content/70">Отправок кейсов пока нет.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($submissions->take(5) as $submission)
                                <div class="rounded-lg border border-base-300 p-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="font-medium">{{ $submission->case?->title }}</p>
                                        <span class="badge badge-outline">
                                            {{ optional($submission->submitted_at)->format('d.m.Y H:i') ?? '—' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-base-content/70 mt-1">
                                        Оценка: {{ $submission->score?->score ?? '—' }} / {{ $submission->score?->max_score ?? '—' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>
        </section>
    </div>