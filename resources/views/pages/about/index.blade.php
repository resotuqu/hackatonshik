<div class="mx-auto mt-8 w-full max-w-6xl space-y-8 sm:mt-12 sm:space-y-12">
    <section class="ui-page-header">
        <div class="space-y-2 pb-5">
            <h1 class="ui-heading-display text-3xl font-bold sm:text-4xl">О Хакатонщике</h1>
            <p class="max-w-2xl text-base-content/70">
                Мы делаем хакатоны доступнее: помогаем участникам находить команды и роли,
                а организаторам — собирать сильные составы без хаоса в чатах и таблицах.
            </p>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-marycard class="card border border-base-300 bg-base-100">
            <p class="text-3xl font-semibold tabular-nums">{{ $impact['hackatons'] ?? 0 }}</p>
            <p class="mt-1 text-sm text-base-content/50">хакатонов проведено</p>
        </x-marycard>
        <x-marycard class="card border border-base-300 bg-base-100">
            <p class="text-3xl font-semibold tabular-nums">{{ $impact['participants'] ?? 0 }}</p>
            <p class="mt-1 text-sm text-base-content/50">участников</p>
        </x-marycard>
        <x-marycard class="card border border-base-300 bg-base-100">
            <p class="text-3xl font-semibold tabular-nums">{{ $impact['teams'] ?? 0 }}</p>
            <p class="mt-1 text-sm text-base-content/50">команд</p>
        </x-marycard>
        <x-marycard class="card border border-base-300 bg-base-100">
            <p class="text-3xl font-semibold tabular-nums">{{ $impact['users'] ?? 0 }}</p>
            <p class="mt-1 text-sm text-base-content/50">пользователей</p>
        </x-marycard>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-marycard title="Наша миссия" class="card border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
            Создать единое пространство для хакатонов России, где участники, команды и организаторы
            быстро находят друг друга и фокусируются на результате.
        </x-marycard>

        <x-marycard title="Для кого мы" class="card border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
            Для участников, которые ищут роль по навыкам, для команд, которым не хватает людей,
            и для партнеров, которым нужен прозрачный отбор.
        </x-marycard>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">Наши ценности</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-marycard title="Прозрачность" class="card border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
                Понятные статусы заявок и единые правила коммуникации.
            </x-marycard>
            <x-marycard title="Удобство" class="card border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
                Простые формы и быстрые сценарии без лишних действий.
            </x-marycard>
            <x-marycard title="Сообщество" class="card border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
                Поддерживаем развитие хакатон-экосистемы через сотрудничество и обмен опытом.
            </x-marycard>
        </div>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">Команда и организаторы</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach ($organizers as $organizer)
                <x-marycard :title="$organizer['name']" class="card border border-base-300 bg-base-100">
                    <p class="text-sm font-medium text-primary">{{ $organizer['role'] }}</p>
                    <p class="mt-2 text-sm text-base-content/80">{{ $organizer['about'] }}</p>
                </x-marycard>
            @endforeach
        </div>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">История проекта</h2>
        <x-marycard class="card border border-base-300 bg-base-100">
            <ul class="timeline timeline-vertical">
                @foreach ($history as $item)
                    <li>
                        @if (! $loop->first)
                            <hr />
                        @endif
                        <div class="timeline-start text-sm text-base-content/70">{{ $item['period'] }}</div>
                        <div class="timeline-middle text-primary">●</div>
                        <div class="timeline-end timeline-box">
                            <p class="font-semibold">{{ $item['title'] }}</p>
                            <p class="text-sm text-base-content/70">{{ $item['description'] }}</p>
                        </div>
                        @if (! $loop->last)
                            <hr />
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-marycard>
    </section>

    <section class="rounded-card border border-base-300 bg-base-100 p-6 sm:p-8">
        <h2 class="font-display text-2xl font-bold">Присоединяйтесь</h2>
        <p class="mt-2 text-base-content/70">
            Если вы участник, соберите профиль и найдите команду. Если вы организатор,
            публикуйте хакатон и управляйте заявками в одном месте.
        </p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="/teams" class="ui-cta-primary">Смотреть команды</a>
            <a href="/hackatons" class="ui-cta-outline">Смотреть хакатоны</a>
        </div>
    </section>
</div>
