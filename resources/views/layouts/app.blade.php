@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    <link rel="icon" href="/hackatonshik_transparent.png" sizes="any">
    <link rel="icon" href="/hackatonshik.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="overflow-x-hidden">

    <header>
        <div class="navbar bg-base-100 shadow-sm justify-between flex flex-row px-3 sm:px-4">
            <div class="flex items-center gap-2">
                <div class="dropdown lg:hidden">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle" aria-label="Открыть меню">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>
                    <ul tabindex="0"
                        class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                        <li><a href="/teams">Команды</a></li>
                        <li><a href="/hackatons">Хакатоны</a></li>
                        <li><a href="/about">О нас</a></li>
                        <li><a href="/contacts">Контакты</a></li>
                        <li><a href="/news">Новости</a></li>
                    </ul>
                </div>
                <a href="/" class="btn btn-ghost text-xl">Хакатонщик</a>
            </div>
            <div class="hidden lg:flex items-center gap-3">
                <a href="/teams">Команды</a>
                <a href="/hackatons">Хакатоны</a>
                <a href="/about">О нас</a>
                <a href="/contacts">Контакты</a>
                <a href="/news">Новости</a>
            </div>
            <div class="flex-none">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full">
                            <img alt="Tailwind CSS Navbar component"
                                src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                        </div>
                    </div>
                    <ul tabindex="-1"
                        class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                        @auth
                            @if(Auth::user()->role === 'user')
                                <li><a href="/profile/teams">Мои команды</a></li>
                                <li><a href="/teams/create">Создать команду</a></li>
                                <li><a href="/profile">Профиль</a></li>
                            @elseif(Auth::user()->role === 'partner')
                                <li><a href="/profile/hackatons">Мои хакатоны</a></li>
                                <li><a href="/hackatons/create">Создать хакатон</a></li>
                                <li><a href="/profile">Профиль</a></li>
                            @else


                            @endif

                            <li class="w-full">
                                <form class="w-full" method="post" action="/logout">@csrf<button class="w-full"
                                        type="submit" href="/logout">Выйти</button></form>
                            </li>

                        @else
                            <li><a href="/register">Зарегистрироваться</a></li>
                            <li><a href="/login">Авторизироваться</a></li>
                        @endauth

                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main class="py-4 px-3 sm:px-6 bg-base-300">
        {{$slot}}
    </main>

    <footer class="footer sm:footer-horizontal bg-base-200 text-base-content p-6 sm:p-10 gap-6">
        <nav>
            <h6 class="footer-title">Основные страницы</h6>
            <a href="/" class="link link-hover">Главная</a>
            <a href="/teams" class="link link-hover">Команды</a>
            <a href="/hackatons" class="link link-hover">Хакатоны</a>
        </nav>
        <nav>
            <h6 class="footer-title">О веб-приложении</h6>
            <a href="/about" class="link link-hover">О нас</a>
            <a href="/contacts" class="link link-hover">Контакты</a>
            <a href="/news" class="link link-hover">Новости</a>
        </nav>
        <nav>
            <h6 class="footer-title">Правовая информация</h6>
            <a href="/privacy-policy" class="link link-hover">Политика конфиденциальности и обработки персональных
                данных</a>
            <a href="/cookie-policy" class="link link-hover">Политика куки файлов</a>
        </nav>
    </footer>
    <footer class="footer bg-base-200 text-base-content border-base-300 border-t px-4 sm:px-10 py-4">
        <aside class="grid-flow-row sm:grid-flow-col items-start sm:items-center gap-2">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                clip-rule="evenodd" class="fill-current">
                <path
                    d="M22.672 15.226l-2.432.811.841 2.515c.33 1.019-.209 2.127-1.23 2.456-1.15.325-2.148-.321-2.463-1.226l-.84-2.518-5.013 1.677.84 2.517c.391 1.203-.434 2.542-1.831 2.542-.88 0-1.601-.564-1.86-1.314l-.842-2.516-2.431.809c-1.135.328-2.145-.317-2.463-1.229-.329-1.018.211-2.127 1.231-2.456l2.432-.809-1.621-4.823-2.432.808c-1.355.384-2.558-.59-2.558-1.839 0-.817.509-1.582 1.327-1.846l2.433-.809-.842-2.515c-.33-1.02.211-2.129 1.232-2.458 1.02-.329 2.13.209 2.461 1.229l.842 2.515 5.011-1.677-.839-2.517c-.403-1.238.484-2.553 1.843-2.553.819 0 1.585.509 1.85 1.326l.841 2.517 2.431-.81c1.02-.33 2.131.211 2.461 1.229.332 1.018-.21 2.126-1.23 2.456l-2.433.809 1.622 4.823 2.433-.809c1.242-.401 2.557.484 2.557 1.838 0 .819-.51 1.583-1.328 1.847m-8.992-6.428l-5.01 1.675 1.619 4.828 5.011-1.674-1.62-4.829z">
                </path>
            </svg>
            <p>
                Хакатонщик
                <br />
                Создаём лучшее с 2026 года
                <br />
                Icons by <a class="underline" href="https://icons8.com" target="_blank">Icons8</a>
            </p>

        </aside>
        {{-- <nav class="md:place-self-center md:justify-self-end">--}}
            {{-- <div class="grid grid-flow-col gap-4">--}}
                {{-- <a>--}}
                    {{-- <svg--}} {{-- xmlns="http://www.w3.org/2000/svg" --}} {{-- width="24" --}} {{-- height="24"
                        --}} {{-- viewBox="0 0 24 24" --}} {{-- class="fill-current">--}}
                        {{-- <path--}} {{--
                            d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z">
                            </path>--}}
                            {{-- </svg>--}}
                            {{-- </a>--}}
                {{-- <a>--}}
                    {{-- <svg--}} {{-- xmlns="http://www.w3.org/2000/svg" --}} {{-- width="24" --}} {{-- height="24"
                        --}} {{-- viewBox="0 0 24 24" --}} {{-- class="fill-current">--}}
                        {{-- <path--}} {{--
                            d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z">
                            </path>--}}
                            {{-- </svg>--}}
                            {{-- </a>--}}
                {{-- <a>--}}
                    {{-- <svg--}} {{-- xmlns="http://www.w3.org/2000/svg" --}} {{-- width="24" --}} {{-- height="24"
                        --}} {{-- viewBox="0 0 24 24" --}} {{-- class="fill-current">--}}
                        {{-- <path--}} {{--
                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z">
                            </path>--}}
                            {{-- </svg>--}}
                            {{-- </a>--}}
                {{-- </div>--}}
            {{-- </nav>--}}
    </footer>


    @livewireScripts
</body>

</html>