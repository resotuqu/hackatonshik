<div class="mx-auto w-full max-w-5xl space-y-6">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="{{ route('profile') }}">Профиль</a></li>
            <li><a href="{{ route('organizer.dashboard') }}">Мои хакатоны</a></li>
            <li class="opacity-70">Заявки</li>
        </ul>
    </div>

    <x-page-header
        title="Заявки на рассмотрении"
        description="Все команды, ожидающие решения организатора. Примите или отклоните заявку — команда получит уведомление."
    />

    @if($this->pendingApplications->isEmpty())
        <section class="ui-surface-card">
            <div class="card-body">
                <x-empty-state
                    title="Нет заявок на рассмотрении"
                    description="Новые заявки появятся здесь, когда команды подадут участие в ваших хакатонах."
                    icon="heroicons:inbox"
                    action-href="{{ route('organizer.dashboard') }}"
                    action-label="К дашборду"
                    secondary-action-href="{{ route('hackatons.index') }}"
                    secondary-action-label="Каталог хакатонов"
                />
            </div>
        </section>
    @else
        <div class="space-y-3">
            @foreach($this->pendingApplications as $application)
                <article wire:key="pending-app-{{ $application->id }}" class="ui-surface-card ui-surface-card--hover">
                    <div class="card-body gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge badge-error badge-sm">На рассмотрении</span>
                                <a href="{{ route('hackatons.show', $application->hackaton) }}" class="link link-hover font-semibold" wire:navigate>
                                    {{ $application->hackaton?->title ?? 'Хакатон' }}
                                </a>
                            </div>
                            <p class="text-lg font-bold leading-tight">
                                {{ $application->team?->title ?? 'Команда' }}
                            </p>
                            @if(filled($application->message))
                                <p class="text-sm text-base-content/70">{{ $application->message }}</p>
                            @endif
                            <p class="text-xs text-base-content/50">
                                Подана: {{ $application->created_at?->format('d.m.Y H:i') ?? '—' }}
                            </p>
                            <a
                                href="{{ route('hackatons.show', $application->hackaton) }}?applications_status=pending#hackaton-tab-participants"
                                class="ui-cta-outline btn-sm w-fit gap-2"
                                wire:navigate
                            >
                                <x-app-icon icon="heroicons:arrow-top-right-on-square" class="h-4 w-4" />
                                Открыть на странице хакатона
                            </a>
                        </div>
                        <div class="flex w-full shrink-0 flex-col gap-2 sm:w-auto sm:min-w-[11rem]">
                            <form method="POST" action="{{ route('hackaton.applications.update', $application) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="accepted" />
                                <button type="submit" class="ui-cta-secondary btn-sm w-full">Принять</button>
                            </form>
                            <form method="POST" action="{{ route('hackaton.applications.update', $application) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected" />
                                <button type="submit" class="ui-cta-outline btn-sm w-full border-error/40 text-error hover:bg-error/10">Отклонить</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
