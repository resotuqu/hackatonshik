<div class="mx-auto w-full max-w-5xl space-y-6">
    <div class="text-sm breadcrumbs">
        <ul>
            <li><a href="/">Главная</a></li>
            <li><a href="{{ route('profile') }}">Профиль</a></li>
            <li><a href="{{ route('organizer.dashboard') }}">Мои хакатоны</a></li>
            <li class="opacity-70">Завершённые</li>
        </ul>
    </div>

    <header class="space-y-2">
        <h1 class="ui-heading-display text-2xl font-black sm:text-3xl">Завершённые хакатоны</h1>
        <p class="max-w-2xl text-sm text-base-content/70">
            События со статусом «Завершён» или «Архив» — итоги, отчёты и материалы доступны со страницы хакатона.
        </p>
    </header>

    @if($this->finishedHackatons->isEmpty())
        <section class="ui-surface-card">
            <div class="card-body">
                <x-empty-state
                    title="Завершённых хакатонов пока нет"
                    description="После окончания события статус обновится автоматически по расписанию — затем хакатон появится в этом списке."
                    icon="heroicons:archive-box"
                    action-href="{{ route('organizer.dashboard') }}"
                    action-label="К дашборду"
                    secondary-action-href="{{ route('hackatons.create') }}"
                    secondary-action-label="Создать хакатон"
                />
            </div>
        </section>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach($this->finishedHackatons as $hackaton)
                <article wire:key="finished-hackaton-{{ $hackaton->id }}" class="ui-surface-card ui-surface-card--hackaton-finished ui-surface-card--hover">
                    <div class="card-body gap-3">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <h2 class="card-title text-lg leading-tight">{{ $hackaton->title }}</h2>
                            <span class="badge {{ $hackaton->status->badgeClass() }} shrink-0">{{ $hackaton->status->label() }}</span>
                        </div>
                        <p class="text-sm text-base-content/70">
                            {{ $hackaton->start_at->format('d.m.Y') }} — {{ $hackaton->end_at->format('d.m.Y') }}
                            · участников: <span class="font-semibold tabular-nums">{{ $hackaton->participants_count }}</span>
                        </p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            <a href="{{ route('hackatons.show', $hackaton) }}" class="ui-cta-outline btn-sm gap-2" wire:navigate>
                                <x-app-icon icon="heroicons:eye" class="h-4 w-4" />
                                Открыть
                            </a>
                            <a href="{{ route('hackatons.edit', $hackaton) }}" class="ui-cta-ghost btn-sm gap-2" wire:navigate>
                                <x-app-icon icon="heroicons:pencil-square" class="h-4 w-4" />
                                Изменить
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
