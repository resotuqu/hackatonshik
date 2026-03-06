<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'Главная'])] class extends Component
{
    //
};
?>

<div class="">
    <div id="start" class="hero min-h-screen">
        <div class="hero-content text-center">
            <div class="max-w-md">
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
                <h1 class="text-5xl font-bold">Приветствуем !</h1>
                <p class="py-6">
                    Хакатонщик &mdash; это место где собираются организаторы и участники.
                </p>
                <a href="#purpose"><button class="btn btn-primary">Давайте начнём &downarrow;</button></a>
            </div>
        </div>
    </div>


    <div id="purpose justify-items-center">
        <h3 class="text-3xl text-center font-bold">Почему стоит использовать Хакатонщика?</h3>

        <x-marycard title="Ищите команду">

        </x-marycard>
    </div>

</div>
