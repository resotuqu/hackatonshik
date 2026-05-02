<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'Новости'])] class extends Component {

    public $news = "
**Привет !**
Теперь мы официально запустили платформу и открыли регистрацию для всех желающих 🚀
*Спасибо, что тестируете платформу !*
";

};
?>

<div class="mx-auto mt-8 w-full space-y-8 sm:mt-12 sm:space-y-12 lg:w-2/3">
    <header class="rounded-3xl border border-primary/15 bg-base-100 px-6 py-5 shadow-md shadow-primary/5 sm:px-8">
        <h1 class="font-display text-3xl font-bold">Новости</h1>
        <p class="mt-1 text-base-content/75">Актуальные события сервиса</p>
    </header>

    <div class="justify-items-center">
        <x-mary-card class="card card-border border-base-300 w-full shadow-md transition-all duration-200 hover:border-primary/25 hover:shadow-lg md:w-2/3 lg:w-1/2">
            <h2 class="text-2xl font-semibold">Запуск сервиса</h2>
            <p class="opacity-80">Выпуск из альфа теста.</p>

            <div class="overflow-hidden rounded-xl bg-base-200 aspect-[16/9] mt-3">
                <img src="/images/livewire.webp" class="w-full h-full object-cover" alt="Тестовая новость">
            </div>

            <div class="mt-4">
                <x-markdown>
                    {{ $news }}
                </x-markdown>
            </div>
        </x-mary-card>

    </div>

</div>