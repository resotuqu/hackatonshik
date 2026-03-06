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

<div class="w-full lg:w-2/3 m-auto space-y-8 sm:space-y-12 mt-8 sm:mt-12">
    <div>
        <h1 class="text-3xl font-bold">Новости</h1>
        <h3 class="font-medium">Актуальные события сервиса</h3>
    </div>

    <div class="justify-items-center">
        <x-mary-card class="card card-border bg-base-100 w-full md:w-2/3 lg:w-1/2">
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