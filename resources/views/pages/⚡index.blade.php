<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'Главная'])] class extends Component
{
    //
};
?>

<div>
    <div class="hero min-h-screen">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <h1 class="text-5xl font-bold">Приветствуем !</h1>
                <p class="py-6">
                    Хакатонщик &mdash; это место где собираются организаторы и участники.
                </p>
                <button class="btn btn-primary">Давайте начнём</button>
            </div>
        </div>
    </div>
</div>
