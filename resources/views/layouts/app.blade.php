<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body>

        <header class="flex flex-row justify-between px-6 py-4 bg-blue-500">
            <div>
                <a class="text-white uppercase font-medium" href="/">Хакатонщик</a>
            </div>
            <div class="flex flex-row space-x-4">
                <a class="text-white font-regular" href="/teams">Команды</a>
                <a class="text-white font-regular" href="/hackatons">Хакатоны</a>
                <a class="text-white font-regular" href="/news">Новости</a>
                <a class="text-white font-regular" href="/about">О нас</a>
            </div>
            <div class="flex flex-row space-x-2 text-white">
                @auth
                    <a href="/profile">{{Auth::user()->email}}</a>
                    <form action="/logout" method="post">@csrf
                        <button type="submit" class="cursor-pointer">Выйти</button>
                    </form>

                    @if(Auth::user()->role == 'user')
                        <a class="text-white font-regular" href="/teams/create">Создать команду</a>
                        <a class="text-white font-regular" href="/profile/teams">Мои команды</a>
                    @elseif(Auth::user()->role == 'partner')
                        <a class="text-white font-regular" href="/hackatons/create">Создать хакатон</a>
                        <a class="text-white font-regular" href="/profile/hackatons">Мои хакатоны</a>
                    @elseif(Auth::user()->role == 'admin')
                        <a class="text-white font-regular" href="/admin-panel">Админ-панель</a>
                    @endif

                @else
                    <a class="text-white font-regular" href="/register">Зарегистрироваться</a>
                    <a class="text-white font-regular" href="/login">Авторизироваться</a>
                @endauth

            </div>
        </header>

        <main class="min-h-screen py-4 px-6">
            {{$slot}}
        </main>

        <footer class="flex flex-row justify-between px-6 py-4 bg-blue-600 text-white">
            <div>&copy;Хакатонщик 2026. Все права защищены</div>
            <div class="flex flex-col">
                <a class="text-white font-regular" href="/">Главная</a>
                <a class="text-white font-regular" href="/teams">Команды</a>
                <a class="text-white font-regular" href="/hackatons">Хакатоны</a>
                <a class="text-white font-regular" href="/news">Новости</a>
                <a class="text-white font-regular" href="/about">О нас</a>
            </div>
            <div class="flex flex-col">
                <p>Наши каналы:</p>
                <a class="text-white font-regular" href="/">MAX</a>
                <a class="text-white font-regular" href="/">OK</a>
                <a class="text-white font-regular" href="/">VK</a>
            </div>
        </footer>


        @livewireScripts
    </body>
</html>
