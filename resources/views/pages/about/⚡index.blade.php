<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'О нас'])] class extends Component
{

};
?>

<div class="w-full lg:w-2/3 m-auto space-y-8 sm:space-y-12 mt-8 sm:mt-12">
    <div>
{{--        <h1 class="text-3xl font-bold">Филип, бросай портвейн! Магратея ждет людей!</h1>--}}
        <h1 class="text-3xl font-bold">О нас</h1>
        <h3 class="font-medium">Мы создаём продукт, который любят и продавцы и потребители</h3>
    </div>


    <x-marycard title="Миссия" class="card card-border">
        <h4 class="font-light">Объединить хакатоны по всей России</h4>
    </x-marycard>

    <x-marycard title="Ценности" class="card card-border">
        <div class="chat chat-start">
            <div class="chat-header">
                Владимир
            </div>
            <div class="chat-bubble">Централизованность</div>
        </div>
        <div class="chat chat-end">
            <div class="chat-header">
                Ещё Владимир
            </div>
            <div class="chat-bubble">Удобство</div>
        </div>
        <div class="chat chat-start">
            <div class="chat-header">
                Опять Владимир
            </div>
            <div class="chat-bubble">Душа</div>
        </div>
    </x-marycard>

    <x-marycard title="Таймлайн" class="card card-border">
            <ul class="timeline timeline-vertical lg:timeline-horizontal m-auto justify-self-center">
                <li>
                    <div class="timeline-start">1945</div>
                    <div class="timeline-middle">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="timeline-end timeline-box justify-items-center">
                        <img src="/images/computer.png" class="w-16 h-16" alt="">
                        <p>Первая в мире ЭВМ</p>
                    </div>
                    <hr />
                </li>
                <li>
                    <hr />
                    <div class="timeline-start">1995</div>
                    <div class="timeline-middle">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="timeline-end timeline-box justify-items-center">
                        <img src="/images/php.png" class="w-16 h-16" alt="">
                        <p>Первая версия PHP</p>
                    </div>
                    <hr />
                </li>
                <li>
                    <hr />
                    <div class="timeline-start">2011</div>
                    <div class="timeline-middle">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="timeline-end timeline-box justify-items-center">
                        <img src="/images/laravel.png" class="w-16 h-16" alt="">
                        <p>Создание Laravel</p>
                    </div>
                    <hr />
                </li>
                <li>
                    <hr />
                    <div class="timeline-start">2018</div>
                    <div class="timeline-middle">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="timeline-end timeline-box justify-items-center">
                        <img src="/images/livewire.webp" class="w-16 h-16" alt="">
                        <p>Создание Livewire</p>
                    </div>
                    <hr />
                </li>
                <li>
                    <hr />
                    <div class="timeline-start">2011</div>
                    <div class="timeline-middle">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                        <div class="timeline-end timeline-box justify-items-center">
                            <img src="/images/mary-ui.png" class="w-16 h-16" alt="">
                            <p>Публикация MaryUI</p>
                        </div>
                    <hr />
                </li>
                <li>
                    <hr />
                    <div class="timeline-start">2026</div>
                    <div class="timeline-middle">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="h-5 w-5"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="timeline-end timeline-box justify-items-center">
                        <img src="/images/hackatonshik_transparent.png" class="w-16 h-16" alt="">
                        <p>Появление Хакатонщика</p>
                    </div>
                </li>
            </ul>
        </x-marycard>


</div>
