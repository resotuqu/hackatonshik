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

        <section class="ui-surface-card shadow-lg">
            <div class="card-body">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-1">
                        <h1 class="ui-heading-display text-2xl font-black sm:text-3xl lg:text-4xl">
                            <span class="bg-linear-to-r from-primary via-accent to-secondary bg-clip-text text-transparent">
                                {{ $hackaton->title }}
                            </span>
                        </h1>
                        <p class="text-sm text-base-content/70">Личный кабинет участника: управляйте командами, следите за дедлайнами и подавайте решения.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('hackatons.show', $hackaton) }}" class="ui-cta-outline btn-sm">
                            <x-app-icon icon="heroicons:arrow-left" class="h-4 w-4" />
                            Страница хакатона
                        </a>
                        <a href="{{ route('hackatons.show', $hackaton) }}#hackaton-tab-cases" class="ui-cta-primary btn-sm">
                            <x-app-icon icon="heroicons:document-text" class="h-4 w-4" />
                            Кейсы
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mt-6">
                    <div class="ui-stat-tile group/stat p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary transition-colors group-hover/stat:bg-primary group-hover/stat:text-primary-content">
                                <x-app-icon icon="heroicons:user-group" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Мои команды</p>
                                <p class="ui-heading-display text-2xl font-black tabular-nums">{{ $teams->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="ui-stat-tile group/stat p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary/10 text-secondary transition-colors group-hover/stat:bg-secondary group-hover/stat:text-secondary-content">
                                <x-app-icon icon="heroicons:envelope" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Мои заявки</p>
                                <p class="ui-heading-display text-2xl font-black tabular-nums">{{ $applications->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="ui-stat-tile group/stat p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-accent/10 text-accent transition-colors group-hover/stat:bg-accent group-hover/stat:text-accent-content">
                                <x-app-icon icon="heroicons:check-circle" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Решения</p>
                                <p class="ui-heading-display text-2xl font-black tabular-nums">{{ $submissions->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if($myCertificates->isNotEmpty())
        <section class="ui-surface-soft shadow-md motion-safe:animate-card-enter">
            <div class="card-body">
                <h2 class="ui-heading-display flex items-center gap-2 text-xl font-bold">
                    <x-app-icon icon="heroicons:academic-cap" class="h-6 w-6 text-primary" />
                    Сертификаты хакатона
                </h2>
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($myCertificates as $certificate)
                        <div class="ui-surface-card p-4 transition-all hover:border-primary/40 hover:shadow-lg">
                            <div class="flex flex-col gap-3">
                                <div class="space-y-1">
                                    <p class="font-bold leading-tight">{{ $certificate->title }}</p>
                                    <p class="text-xs text-base-content/60">Выдан: {{ $certificate->issued_at?->format('d.m.Y') ?? '—' }}</p>
                                </div>
                                <a href="{{ route('certificates.download', $certificate) }}" class="ui-cta-primary btn-sm w-full">
                                    <x-app-icon icon="heroicons:arrow-down-tray" class="h-4 w-4" />
                                    Скачать PDF
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
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

            <article>
                @if($teams->isNotEmpty())
                    <livewire:team-chat :team="$teams->first()" />
                @endif
            </article>
        </section>
    </div>