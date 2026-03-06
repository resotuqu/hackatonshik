<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'Главная'])] class extends Component {
    //
};
?>

<div class="">
    <div id="start" class="hero min-h-[80vh] sm:min-h-screen">
        <div class="hero-content text-center px-2">
            <div class="w-full max-w-md">
                <div class="hover-3d">
                    <!-- content -->
                    <figure class="max-w-50 rounded-2xl">
                        <div>
                            <x-marybadge class="badge-warning" value="ХакатонКун" />
                            <img src="/images/hackatonshik_transparent.png" alt="3D card" />
                        </div>

                    </figure>
                    <!-- 8 empty divs needed for the 3D effect -->
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <h1 class="text-4xl sm:text-5xl font-bold">Приветствуем !</h1>
                <p class="py-6">
                    Хакатонщик &mdash; это место где собираются организаторы и участники.
                </p>
                <a href="/#purpose"><button class="btn btn-primary">Давайте начнём &downarrow;</button></a>
            </div>
        </div>
    </div>


    <div id="purpose" class="justify-items-center ">
        <h3 class="text-3xl text-center font-bold ">Почему стоит использовать Хакатонщика?</h3>

        <div class="flex flex-col md:flex-row gap-4 w-full max-w-5xl px-2">
            {{-- title="Ищите команду под свои навыки" --}}
            <x-marycard class="mt-8">
                <x-marycard title="Фильтрация под вас" class="card card-border w-full bg-base-200">
                    Найдите роль под свои возможности
                    <x-slot:figure>
                        <img src="/images/pros-1.png" />
                    </x-slot:figure>
                    </x-card>
                </x-marycard>
                <x-marycard class="mt-8">
                    <x-marycard title="Удобство использования" class=" card card-border w-full bg-base-200">
                        Больше не надо отправлять документы на почту или в чат сотрудника
                        <x-slot:figure>
                            <img src="/images/pros-3.png" />
                        </x-slot:figure>
                        </x-card>
                    </x-marycard>
                </x-marycard>
        </div>
    </div>

</div>