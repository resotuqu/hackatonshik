<div class="mx-auto mt-8 w-full max-w-6xl space-y-8 sm:mt-12 sm:space-y-12">
    <section class="ui-page-hero">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-3">
                <h1 class="font-display text-3xl font-bold sm:text-4xl">О Хакатонщике</h1>
                <p class="max-w-2xl text-base-content/75">
                    Мы делаем хакатоны доступнее: помогаем участникам находить команды и роли,
                    а организаторам — собирать сильные составы без хаоса в чатах и таблицах.
                </p>
            </div>
            <img src="/logo.svg" class="mx-auto h-auto w-48 sm:mx-0 sm:w-64" alt="Логотип Хакатонщика">
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-marycard title="Проведено хакатонов" class="card card-border border-base-300 bg-base-100">
            <p class="text-3xl font-semibold">{{ $impact['hackatons'] ?? 0 }}</p>
        </x-marycard>
        <x-marycard title="Участников" class="card card-border border-base-300 bg-base-100">
            <p class="text-3xl font-semibold">{{ $impact['participants'] ?? 0 }}</p>
        </x-marycard>
        <x-marycard title="Команд" class="card card-border border-base-300 bg-base-100">
            <p class="text-3xl font-semibold">{{ $impact['teams'] ?? 0 }}</p>
        </x-marycard>
        <x-marycard title="Пользователей" class="card card-border border-base-300 bg-base-100">
            <p class="text-3xl font-semibold">{{ $impact['users'] ?? 0 }}</p>
        </x-marycard>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-marycard title="Наша миссия" class="card card-border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
            Создать единое пространство для хакатонов России, где участники, команды и организаторы
            быстро находят друг друга и фокусируются на результате.
        </x-marycard>

        <x-marycard title="Для кого мы" class="card card-border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
            Для участников, которые ищут роль по навыкам, для команд, которым не хватает людей,
            и для партнеров, которым нужен прозрачный отбор.
        </x-marycard>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">Наши ценности</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-marycard title="Прозрачность" class="card card-border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
                Понятные статусы заявок и единые правила коммуникации.
            </x-marycard>
            <x-marycard title="Удобство" class="card card-border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
                Простые формы и быстрые сценарии без лишних действий.
            </x-marycard>
            <x-marycard title="Сообщество" class="card card-border border-base-300 bg-base-100 transition-colors hover:border-primary/25">
                Поддерживаем развитие хакатон-экосистемы через сотрудничество и обмен опытом.
            </x-marycard>
        </div>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">Команда и организаторы</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach ($organizers as $organizer)
                <x-marycard :title="$organizer['name']" class="card card-border border-base-300 bg-base-100">
                    <p class="text-sm font-medium text-primary">{{ $organizer['role'] }}</p>
                    <p class="mt-2 text-sm text-base-content/80">{{ $organizer['about'] }}</p>
                </x-marycard>
            @endforeach
        </div>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">История проекта</h2>
        <x-marycard class="card card-border border-base-300 bg-base-100">
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

    <section class="ui-page-hero">
        <h2 class="font-display text-2xl font-bold">Присоединяйтесь</h2>
        <p class="mt-2 text-base-content/75">
            Если вы участник, соберите профиль и найдите команду. Если вы организатор,
            публикуйте хакатон и управляйте заявками в одном месте.
        </p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="/teams" class="btn btn-primary">Смотреть команды</a>
            <a href="/hackatons" class="btn btn-outline">Смотреть хакатоны</a>
        </div>
    </section>
</div>
