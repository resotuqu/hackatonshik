<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'Контакты'])] class extends Component {

};
?>

<div class="mx-auto mt-8 w-full space-y-8 sm:mt-12 sm:space-y-12 lg:w-2/3">
    <section class="rounded-3xl border border-primary/15 bg-base-100 p-6 shadow-md shadow-primary/5 sm:p-8">
        <h1 class="font-display text-3xl font-bold">Контакты</h1>
        <p class="mt-1 text-base-content/75">Свяжитесь с нами</p>
        <div class="mt-6 space-y-3 text-base">
            <p>
                Адрес электронной почты:
                <a class="link link-primary font-medium" href="mailto:sekhmych@yandex.ru">sekhmych@yandex.ru</a>
            </p>
            <p>
                Телефон:
                <a class="link link-primary font-medium" href="tel:+79248605316">+7 (924) 860-53-16</a>
            </p>
        </div>
    </section>
</div>