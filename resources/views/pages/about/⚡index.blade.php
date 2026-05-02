<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'О нас'])] class extends Component
{

};
?>

<div class="mx-auto mt-8 w-full space-y-8 sm:mt-12 sm:space-y-12 lg:w-2/3">
    <section class="rounded-3xl border border-primary/15 bg-base-100 p-6 shadow-md shadow-primary/5 sm:p-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-3">
                <h1 class="font-display text-3xl font-bold sm:text-4xl">О Хакатонщике</h1>
                <p class="max-w-2xl text-base-content/75">
                    Мы делаем хакатоны доступнее: помогаем участникам находить команды и роли,
                    а организаторам — собирать сильные составы без хаоса в чатах и таблицах.
                </p>
            </div>
            <img src="/logo.svg" class="mx-auto h-auto w-32 sm:mx-0 sm:w-40" alt="Логотип Хакатонщика">
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-marycard title="Наша миссия" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
            Создать единое пространство для хакатонов России, где участники, команды и организаторы
            быстро находят друг друга и фокусируются на результате.
        </x-marycard>

        <x-marycard title="Для кого мы" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
            Для участников, которые ищут роль по навыкам, для команд, которым не хватает людей,
            и для партнеров, которым нужен прозрачный отбор.
        </x-marycard>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">Наши ценности</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-marycard title="Прозрачность" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
                Понятные статусы заявок и единые правила коммуникации.
            </x-marycard>
            <x-marycard title="Удобство" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
                Простые формы и быстрые сценарии без лишних действий.
            </x-marycard>
            <x-marycard title="Сообщество" class="card card-border border-base-300 bg-base-100 shadow-sm transition-all duration-200 hover:border-primary/25 hover:shadow-md">
                Поддерживаем развитие хакатон-экосистемы через сотрудничество и обмен опытом.
            </x-marycard>
        </div>
    </section>

    <section>
        <h2 class="mb-4 font-display text-2xl font-bold">Как это работает</h2>
        <x-marycard class="card card-border border-base-300 bg-base-100 shadow-sm">
            <ul class="timeline timeline-vertical">
                <li>
                    <div class="timeline-start text-sm text-base-content/70">Шаг 1</div>
                    <div class="timeline-middle text-primary">●</div>
                    <div class="timeline-end timeline-box">Создайте команду или хакатон.</div>
                    <hr />
                </li>
                <li>
                    <hr />
                    <div class="timeline-start text-sm text-base-content/70">Шаг 2</div>
                    <div class="timeline-middle text-primary">●</div>
                    <div class="timeline-end timeline-box">Подайте заявку на участие или роль в команде.</div>
                    <hr />
                </li>
                <li>
                    <hr />
                    <div class="timeline-start text-sm text-base-content/70">Шаг 3</div>
                    <div class="timeline-middle text-primary">●</div>
                    <div class="timeline-end timeline-box">Получайте подтверждение и начинайте работу над проектом.</div>
                </li>
            </ul>
        </x-marycard>
    </section>

    <section class="rounded-2xl border border-primary/15 bg-base-100 p-6 shadow-md shadow-primary/5">
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
