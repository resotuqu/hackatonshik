@if($hackatons->isEmpty())
    <section class="ui-surface-soft motion-safe:animate-card-enter">
        <div class="card-body space-y-4 py-10 sm:py-12">
            <div class="mx-auto max-w-lg text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-secondary/15 ring-2 ring-secondary/25">
                    <x-app-icon icon="heroicons:rocket-launch" class="h-8 w-8 text-secondary" />
                </div>
                <x-empty-state
                    title="Пока нет хакатонов"
                    description="Создайте событие для участников — настройте регистрацию, кейсы и судейство в одном месте."
                    icon="heroicons:sparkles"
                    action-href="{{ route('hackatons.create') }}"
                    action-label="Создать хакатон"
                    secondary-action-href="{{ route('hackatons.index') }}"
                    secondary-action-label="Каталог хакатонов"
                />
            </div>
        </div>
    </section>
@else
    @if(($showGlobalPendingStrip ?? false) && (($globalPending['applications'] ?? 0) > 0 || ($globalPending['judgeInvitations'] ?? 0) > 0))
        <section class="rounded-2xl border border-warning/30 bg-warning/10 p-4 sm:p-5 motion-safe:animate-card-enter" aria-label="Глобальные задачи">
            <h2 class="ui-heading-display text-sm font-bold uppercase tracking-widest text-warning">Требуют внимания (все хакатоны)</h2>
            <div class="mt-3 flex flex-wrap gap-4 text-sm">
                @if(($globalPending['applications'] ?? 0) > 0)
                    <a href="{{ route('profile.hackatons.applications') }}" class="link link-hover font-medium" wire:navigate>
                        Заявки команд: {{ $globalPending['applications'] }}
                    </a>
                @endif
                @if(($globalPending['judgeInvitations'] ?? 0) > 0)
                    @if(! empty($judgeInvitationsFocusHackatonId))
                        <a
                            href="{{ route('hackatons.show', $judgeInvitationsFocusHackatonId) }}#hackaton-tab-organization"
                            class="link link-hover font-medium"
                            wire:navigate
                        >
                            Приглашения судей (ожидают): {{ $globalPending['judgeInvitations'] }}
                        </a>
                    @else
                        <span class="font-medium text-base-content/80">
                            Приглашения судей (ожидают): {{ $globalPending['judgeInvitations'] }}
                        </span>
                    @endif
                @endif
            </div>
            <p class="mt-2 text-xs text-base-content/60">Откройте страницу хакатона — вкладка «Организация» — чтобы обработать приглашения.</p>
        </section>
    @endif

    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="ui-heading-display text-2xl font-black sm:text-3xl lg:text-4xl">Дашборд организатора</h1>
            <p class="mt-1 max-w-xl text-sm text-base-content/70">
                Ключевые метрики, текущий фокус и быстрые действия. Ниже — полный список ваших хакатонов.
            </p>
        </div>
        <a href="{{ route('hackatons.create') }}" class="ui-cta-secondary btn-sm sm:btn-md shrink-0 gap-2 self-start sm:self-auto" wire:navigate>
            <x-app-icon icon="heroicons:plus" class="h-5 w-5" />
            Новый хакатон
        </a>
    </header>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="ui-stat-tile group/stat p-4 motion-safe:animate-card-enter">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary/10 text-secondary transition-colors group-hover/stat:bg-secondary group-hover/stat:text-secondary-content">
                    <x-app-icon icon="heroicons:flag" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Активные хакатоны</p>
                    <p class="ui-heading-display text-2xl font-black tabular-nums">{{ $summary['activeHackatons'] }}</p>
                </div>
            </div>
        </div>
        <div class="ui-stat-tile group/stat p-4 motion-safe:animate-card-enter delay-100">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-error/10 text-error transition-colors group-hover/stat:bg-error group-hover/stat:text-error-content">
                    <x-app-icon icon="heroicons:inbox" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Заявки на рассмотрении</p>
                    <p class="ui-heading-display text-2xl font-black tabular-nums">{{ $summary['pendingApplications'] }}</p>
                </div>
            </div>
        </div>
        <div class="ui-stat-tile group/stat p-4 motion-safe:animate-card-enter delay-200">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary transition-colors group-hover/stat:bg-primary group-hover/stat:text-primary-content">
                    <x-app-icon icon="heroicons:user-group" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Участники (роли)</p>
                    <p class="ui-heading-display text-2xl font-black tabular-nums">{{ $summary['participantsTotal'] }}</p>
                </div>
            </div>
        </div>
        <div class="ui-stat-tile group/stat p-4 motion-safe:animate-card-enter delay-300">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-accent/10 text-accent transition-colors group-hover/stat:bg-accent group-hover/stat:text-accent-content">
                    <x-app-icon icon="heroicons:archive-box" class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-base-content/50">Всего создано</p>
                    <p class="ui-heading-display text-2xl font-black tabular-nums">{{ $summary['hackatonsTotal'] }}</p>
                </div>
            </div>
        </div>
    </section>

    @if($featuredHackaton)
        <section class="ui-surface-card ui-surface-card--hackaton-active shadow-lg motion-safe:animate-card-enter" aria-label="Текущий хакатон">
            <div class="card-body gap-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:gap-4">
                        <div class="aspect-video w-full shrink-0 overflow-hidden rounded-xl bg-base-200 sm:max-w-[14rem]">
                            @if(filled($featuredHackaton->image_url))
                                <img
                                    src="/uploads/{{ $featuredHackaton->image_url }}"
                                    class="h-full w-full object-cover"
                                    alt=""
                                />
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-linear-to-br from-secondary/15 to-primary/10">
                                    <x-app-icon icon="heroicons:photo" class="h-12 w-12 text-base-content/30" />
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge {{ $featuredHackaton->status->badgeClass() }}">{{ $featuredHackaton->status->label() }}</span>
                                @if($featuredHackaton->pending_applications_count > 0)
                                    <span class="badge badge-error gap-1">
                                        <x-app-icon icon="heroicons:exclamation-triangle" class="h-3.5 w-3.5" />
                                        {{ $featuredHackaton->pending_applications_count }} заявок
                                    </span>
                                @endif
                            </div>
                            <h2 class="ui-heading-display text-xl font-black sm:text-2xl">{{ $featuredHackaton->title }}</h2>
                            <dl class="grid grid-cols-1 gap-2 text-sm text-base-content/75 sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-base-content/50">Старт — финиш</dt>
                                    <dd class="font-medium tabular-nums">
                                        {{ $featuredHackaton->start_at->format('d.m.Y H:i') }} — {{ $featuredHackaton->end_at->format('d.m.Y H:i') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-base-content/50">Дедлайн регистрации</dt>
                                    <dd class="font-medium tabular-nums">
                                        {{ $featuredHackaton->registration_deadline_at?->format('d.m.Y H:i') ?? '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-base-content/50">Участники</dt>
                                    <dd class="font-medium tabular-nums">{{ $featuredHackaton->participants_count }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-base-content/50">Отправки решений</dt>
                                    <dd class="font-medium tabular-nums">{{ $featuredHackaton->submissions_count }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    <div class="flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap lg:w-auto lg:max-w-sm lg:flex-col">
                        <a href="{{ route('hackatons.show', $featuredHackaton) }}" class="ui-cta-outline btn-sm w-full gap-2 sm:flex-1 lg:flex-none" wire:navigate>
                            <x-app-icon icon="heroicons:eye" class="h-4 w-4" />
                            Страница хакатона
                        </a>
                        <a href="{{ route('hackatons.edit', $featuredHackaton) }}" class="ui-cta-secondary btn-sm w-full gap-2 sm:flex-1 lg:flex-none" wire:navigate>
                            <x-app-icon icon="heroicons:pencil-square" class="h-4 w-4" />
                            Редактировать
                        </a>
                        <a
                            href="{{ route('hackatons.show', $featuredHackaton) }}?applications_status=pending#hackaton-tab-participants"
                            class="ui-cta-outline btn-sm w-full gap-2 border-error/30 sm:flex-1 lg:flex-none"
                            wire:navigate
                        >
                            <x-app-icon icon="heroicons:inbox" class="h-4 w-4 text-error" />
                            Заявки
                        </a>
                        <a href="{{ route('profile.hackatons.scoring') }}" class="ui-cta-outline btn-sm w-full gap-2 sm:flex-1 lg:flex-none" wire:navigate>
                            <x-app-icon icon="heroicons:clipboard-document-check" class="h-4 w-4" />
                            Оценка
                        </a>
                        <button type="button" class="ui-cta-outline btn-sm w-full gap-2 sm:flex-1 lg:flex-none" wire:click="participantsHackaton({{ $featuredHackaton->id }})">
                            <x-app-icon icon="heroicons:user-group" class="h-4 w-4" />
                            Участники
                        </button>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="ui-surface-soft p-4 sm:p-5 motion-safe:animate-card-enter">
        <h2 class="ui-heading-display mb-3 text-sm font-bold uppercase tracking-widest text-base-content/55">Быстрые действия</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('hackatons.create') }}" class="ui-cta-secondary btn-md h-auto min-h-[3.25rem] flex-col gap-1 py-3 sm:flex-row sm:gap-2" wire:navigate>
                <x-app-icon icon="heroicons:plus-circle" class="h-6 w-6" />
                <span>Создать хакатон</span>
            </a>
            <a href="{{ route('profile.hackatons.applications') }}" class="ui-cta-outline btn-md h-auto min-h-[3.25rem] flex-col gap-1 border-error/25 py-3 sm:flex-row sm:gap-2" wire:navigate>
                <x-app-icon icon="heroicons:inbox" class="h-6 w-6 text-error" />
                <span>Заявки</span>
            </a>
            <a href="{{ route('profile.hackatons.scoring') }}" class="ui-cta-outline btn-md h-auto min-h-[3.25rem] flex-col gap-1 py-3 sm:flex-row sm:gap-2" wire:navigate>
                <x-app-icon icon="heroicons:clipboard-document-check" class="h-6 w-6" />
                <span>Оценка работ</span>
            </a>
            <a href="{{ route('profile.hackatons.finished') }}" class="ui-cta-outline btn-md h-auto min-h-[3.25rem] flex-col gap-1 py-3 sm:flex-row sm:gap-2" wire:navigate>
                <x-app-icon icon="heroicons:archive-box" class="h-6 w-6" />
                <span>Завершённые</span>
            </a>
        </div>
    </section>

    <div class="flex flex-col gap-2 border-b border-base-300/60 pb-2 sm:flex-row sm:items-end sm:justify-between">
        <h2 class="ui-heading-display text-xl font-bold">Все хакатоны</h2>
        <p class="text-xs text-base-content/60">Управление карточками, участниками и удалением.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($hackatons as $hackaton)
            <x-marycard wire:key="hackaton-card-{{ $hackaton->id }}" class="card card-border @if($hackaton->pending_applications_count > 0) border-error/40 ring-1 ring-error/20 @endif">
                <div class="aspect-video overflow-hidden rounded-xl bg-base-200">
                    @if(filled($hackaton->image_url))
                        <img src="/uploads/{{ $hackaton->image_url }}" class="h-full w-full object-cover" alt="{{ $hackaton->title }}">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-linear-to-br from-secondary/15 to-primary/10">
                            <x-app-icon icon="heroicons:photo" class="h-12 w-12 text-base-content/30" />
                        </div>
                    @endif
                </div>
                <div class="mt-2 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="card-title flex-1 leading-tight">{{ $hackaton->title }}</p>
                        <span class="badge {{ $hackaton->status->badgeClass() }} badge-sm shrink-0">{{ $hackaton->status->label() }}</span>
                    </div>
                    @if($hackaton->pending_applications_count > 0)
                        <div class="rounded-lg border border-error/30 bg-error/5 px-2 py-1.5 text-xs font-medium text-error">
                            {{ $hackaton->pending_applications_count }} заявок на рассмотрении
                        </div>
                    @endif
                    <x-mary-card class="card card-border bg-base-200">
                        <p>Участники: <span class="font-semibold tabular-nums">{{ $hackaton->participants_count }}</span></p>
                        <p class="text-sm text-base-content/80">
                            {{ $hackaton->start_at->format('d.m.Y H:i') }} — {{ $hackaton->end_at->format('d.m.Y H:i') }}
                        </p>
                        @if($hackaton->registration_deadline_at)
                            <p class="text-xs text-base-content/65">
                                Регистрация до: {{ $hackaton->registration_deadline_at->format('d.m.Y H:i') }}
                            </p>
                        @endif
                    </x-mary-card>
                </div>

                <x-slot:actions>
                    <a href="{{ route('hackatons.show', $hackaton) }}" wire:navigate>
                        <x-marybutton class="btn-ghost" label="Просмотреть" />
                    </a>
                    <x-marybutton class="btn-primary" label="Изменить" wire:click="editHackaton({{ $hackaton->id }})" />
                    <x-marybutton class="btn-secondary" label="Участники" wire:click="participantsHackaton({{ $hackaton->id }})" />
                    <x-marybutton class="btn-error" label="Удалить" wire:click="showDeleteHackatonModal({{ $hackaton->id }})" />
                </x-slot:actions>
            </x-marycard>
        @endforeach
    </div>
@endif
