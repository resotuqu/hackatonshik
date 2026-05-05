    <div class="mx-auto w-full max-w-7xl space-y-6" wire:loading.class="pointer-events-none opacity-60">
        <div wire:loading.delay.shortest class="fixed inset-x-0 top-20 z-50 flex justify-center px-4" aria-live="polite">
            <div class="alert alert-info shadow-lg">
                <span class="loading loading-spinner loading-sm"></span>
                <span>Загрузка…</span>
            </div>
        </div>

        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="/">Главная</a></li>
                <li><a href="/profile">Профиль</a></li>
                <li><a href="{{ route('hackatons.show', $hackaton) }}">{{ $hackaton->title }}</a></li>
                <li class="opacity-70">Мой хакатон</li>
            </ul>
        </div>

        <section class="card border border-base-200 bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="ui-heading-display card-title text-2xl">{{ $hackaton->title }}</h1>
                        <p class="text-sm text-base-content/70">Личный кабинет участника с ключевыми действиями и дедлайнами.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('hackatons.show', $hackaton) }}" class="btn btn-outline btn-sm">Страница хакатона</a>
                        <a href="{{ route('hackatons.show', $hackaton) }}#hackaton-tab-cases" class="btn btn-primary btn-sm">Кейсы</a>
                        <a href="/profile/certificates" class="btn btn-ghost btn-sm">Мои сертификаты</a>
                    </div>
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

        @if($myCertificates->isNotEmpty())
        <section class="card border border-base-200 bg-base-100 shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-lg">Сертификаты по этому хакатону</h2>
                <ul class="mt-2 divide-y divide-base-200 rounded-lg border border-base-200">
                    @foreach($myCertificates as $certificate)
                        <li class="flex flex-wrap items-center justify-between gap-2 px-3 py-2">
                            <span class="font-medium">{{ $certificate->title }}</span>
                            <span class="text-sm text-base-content/70">{{ $certificate->issued_at?->format('d.m.Y') ?? '—' }}</span>
                            <a href="{{ route('certificates.download', $certificate) }}" class="btn btn-xs btn-outline">Скачать</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
        @endif

        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <article class="card border border-base-200 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-lg">Статусы заявок</h2>
                    @if($applications->isEmpty())
                        <x-empty-state
                            embedded
                            title="Заявок пока нет"
                            description="Подайте заявку командой со страницы хакатона, чтобы статус отобразился здесь."
                            icon="heroicons:inbox"
                        />
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
                        <x-empty-state
                            embedded
                            title="Документы не заданы"
                            description="Организатор пока не добавил обязательные документы."
                            icon="heroicons:document-text"
                        />
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
                        <x-empty-state
                            embedded
                            title="Дедлайнов нет"
                            description="Активных дедлайнов по кейсам сейчас нет."
                            icon="heroicons:clock"
                        />
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
                        <x-empty-state
                            embedded
                            title="Отправок нет"
                            description="Отправки решений кейсов появятся здесь после загрузки."
                            icon="heroicons:paper-airplane"
                        />
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