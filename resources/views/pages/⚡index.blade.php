<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'Главная'])] class extends Component {
    //
};

?>

<div class="mx-auto w-full max-w-7xl space-y-12">
    <section id="start" class="hero min-h-[70vh] rounded-3xl bg-base-100 px-4 py-8 shadow-sm sm:min-h-[75vh]">
        <div class="hero-content text-center">
            <div class="w-full max-w-xl space-y-5">
                <figure class="mx-auto max-w-52 rounded-2xl bg-base-200 p-3">
                    <x-marybadge class="badge-warning mb-2" value="ХакатонКун" />
                    <img src="/images/hackatonshik_transparent.png" alt="Логотип Хакатонщика" />
                </figure>
                <h1 class="text-4xl font-bold sm:text-5xl">Путь участника и организатора в одном месте</h1>
                <p class="text-base-content/80">
                    Создавайте команды, подавайте заявки, решайте кейсы и получайте сертификаты без переписки в чатах и почте.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    @auth
                        <a href="/hackatons" class="btn btn-primary">Перейти к хакатонам</a>
                        <a href="/teams" class="btn btn-outline">Открыть мои команды</a>
                    @else
                        <a href="/register" class="btn btn-primary">Зарегистрироваться и начать</a>
                        <a href="/login" class="btn btn-outline">У меня уже есть аккаунт</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <section id="purpose" class="space-y-6">
        <h2 class="text-center text-3xl font-bold">Как это работает</h2>
        <p class="mx-auto max-w-3xl text-center text-base-content/80">
            Четкая последовательность шагов помогает быстро понять, что делать дальше на каждом этапе.
        </p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-marycard title="1. Создайте команду" class="card card-border bg-base-100 shadow-sm">
                Соберите участников и распределите роли в пару кликов.
            </x-marycard>

            <x-marycard title="2. Подайте заявку" class="card card-border bg-base-100 shadow-sm">
                Выберите хакатон и отправьте заявку от команды.
            </x-marycard>

            <x-marycard title="3. Решайте кейсы" class="card card-border bg-base-100 shadow-sm">
                Заполняйте поля кейса и загружайте материалы в одном месте.
            </x-marycard>

            <x-marycard title="4. Получайте результаты" class="card card-border bg-base-100 shadow-sm">
                Следите за статусами, анонсами и сертификатами прямо в профиле.
            </x-marycard>
        </div>
    </section>

    <section class="space-y-6">
        <h2 class="text-center text-3xl font-bold">Почему это удобно</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-marycard title="Фильтрация под вас" class="card card-border bg-base-100 shadow-sm">
                Найдите роль под свои возможности.
                <x-slot:figure>
                    <img src="/images/pros-1.png" alt="Фильтрация ролей" />
                </x-slot:figure>
            </x-marycard>

            <x-marycard title="Удобство использования" class="card card-border bg-base-100 shadow-sm">
                Больше не надо отправлять документы на почту или в чат сотрудника.
                <x-slot:figure>
                    <img src="/images/pros-3.png" alt="Удобство использования" />
                </x-slot:figure>
            </x-marycard>
        </div>
    </section>
</div>
