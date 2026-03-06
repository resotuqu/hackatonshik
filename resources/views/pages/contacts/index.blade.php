<?php

use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::app', ['title' => 'Контакты'])] class extends Component {

};
?>

<div class="w-full lg:w-2/3 m-auto space-y-8 sm:space-y-12 mt-8 sm:mt-12">
    <div>
        <h1 class="text-3xl font-bold">Контакты</h1>
        <h3 class="font-medium">Свяжитесь с нами</h3>
    </div>

    <div class="">
        <p>Адрес электронной почты: <a class="underline" href="mailto:sekhmych@yandex.ru">sekhmych@yandex.ru</a></p>
        <p>Телефон: <a class="underline" href="tel:+79248605316">+7 (924) 860-53-16</a></p>
    </div>

</div>