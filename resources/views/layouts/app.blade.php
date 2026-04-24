@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Платформа для команд, хакатонов и совместных проектов.')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', config('app.name'))))">
    <meta property="og:description" content="@yield('og_description', $__env->yieldContent('meta_description', 'Платформа для команд, хакатонов и совместных проектов.'))">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <title>
        @isset($title)
            {{ $title }}
        @else
            @yield('title', config('app.name'))
        @endisset
    </title>
    <link rel="icon" href="/hackatonshik_transparent.png" sizes="any">
    <link rel="icon" href="/hackatonshik.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script>
        (function () {
            const cookieName = 'theme';
            const cookieMatch = document.cookie.match(new RegExp('(?:^|; )' + cookieName + '=([^;]*)'));
            const savedTheme = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
            const theme = savedTheme === 'dark' ? 'dark' : 'cupcake';

            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen overflow-x-hidden bg-base-300">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-base-100 focus:px-4 focus:py-2 focus:shadow">
        Перейти к основному контенту
    </a>
    <div class="drawer lg:drawer-open min-h-screen">
        <input id="main-nav-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex min-h-screen flex-col">
            <header class="navbar bg-base-100 shadow-sm px-3 sm:px-4 lg:hidden">
                <div class="flex items-center gap-2">
                    <label for="main-nav-drawer" class="btn btn-ghost btn-circle drawer-button" aria-label="Открыть меню">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </label>
                    <a href="{{ route('home') }}" class="btn btn-ghost text-xl">Хакатонщик</a>
                </div>
            </header>

            <main id="main-content" class="flex-1" tabindex="-1">
                <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    @if (session('success') || session('error') || session('warning'))
                        <div class="toast toast-top toast-end z-70">
                            @if (session('success'))
                                <div class="alert alert-success shadow-lg">
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-error shadow-lg">
                                    <span>{{ session('error') }}</span>
                                </div>
                            @endif
                            @if (session('warning'))
                                <div class="alert alert-warning shadow-lg">
                                    <span>{{ session('warning') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @hasSection('slot')
                        @yield('slot')
                    @elseif (isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </div>
            </main>

            <footer class="footer sm:footer-horizontal bg-base-200 text-base-content px-6 py-8 sm:px-10 sm:py-10 gap-6">
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
                    <a href="/privacy-policy" class="link link-hover">Политика конфиденциальности и обработки персональных данных</a>
                    <a href="/cookie-policy" class="link link-hover">Политика куки файлов</a>
                </nav>
            </footer>
        </div>

        <div class="drawer-side z-40">
            <label for="main-nav-drawer" aria-label="Закрыть меню" class="drawer-overlay lg:hidden"></label>
            <aside class="min-h-full w-72 bg-base-100 border-r border-base-200 flex flex-col p-4" aria-label="Основная навигация">
                <div class="flex items-center justify-between gap-2">
                    <a href="{{ route('home') }}" class="btn btn-ghost text-xl px-2">Хакатонщик</a>
                    @auth
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                                <div class="w-10 rounded-full">
                                    <img alt="Аватар пользователя" src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                                </div>
                            </div>
                            <ul tabindex="-1" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">
                                <li><a href="/profile">Профиль</a></li>
                                <li class="w-full">
                                    <form class="w-full" method="post" action="/logout">
                                        @csrf
                                        <button class="w-full text-left" type="submit">Выйти</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>

                <ul class="menu mt-4 w-full gap-1" role="navigation" aria-label="Меню сайта">
                    <li class="menu-title"><span>Основное</span></li>
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Главная</a></li>
                    <li><a href="/teams" class="{{ request()->is('teams*') ? 'active' : '' }}">Команды</a></li>
                    <li><a href="/hackatons" class="{{ request()->is('hackatons*') ? 'active' : '' }}">Хакатоны</a></li>
                    <li><a href="/about" class="{{ request()->is('about') ? 'active' : '' }}">О нас</a></li>
                    <li><a href="/contacts" class="{{ request()->is('contacts') ? 'active' : '' }}">Контакты</a></li>
                    <li><a href="/news" class="{{ request()->is('news*') ? 'active' : '' }}">Новости</a></li>

                    <li class="menu-title mt-3"><span>Мой кабинет</span></li>
                    @auth
                        @if (Auth::user()->role === 'user')
                            <li><a href="/profile/teams" class="{{ request()->is('profile/teams*') ? 'active' : '' }}">Мои команды</a></li>
                            <li><a href="/teams/create" class="{{ request()->is('teams/create') ? 'active' : '' }}">Создать команду</a></li>
                        @elseif (Auth::user()->role === 'partner')
                            <li><a href="/profile/hackatons" class="{{ request()->is('profile/hackatons*') ? 'active' : '' }}">Мои хакатоны</a></li>
                            <li><a href="/hackatons/create" class="{{ request()->is('hackatons/create') ? 'active' : '' }}">Создать хакатон</a></li>
                        @elseif (Auth::user()->role === 'judge')
                            <li><a href="/hackatons" class="{{ request()->is('hackatons*') ? 'active' : '' }}">Назначенные хакатоны</a></li>
                        @elseif (Auth::user()->role === 'admin')
                            <li><a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">Админ-панель</a></li>
                        @endif
                        <li><a href="/profile" class="{{ request()->is('profile') ? 'active' : '' }}">Профиль</a></li>
                        <li><a href="{{ route('profile.public.show', ['user' => Auth::user()->nickname]) }}" class="{{ request()->is('u/*') ? 'active' : '' }}">Публичный профиль</a></li>
                        <li><a href="/profile/certificates" class="{{ request()->is('profile/certificates*') ? 'active' : '' }}">Сертификаты</a></li>
                    @else
                        <li><a href="/login" class="font-medium {{ request()->is('login') ? 'active' : '' }}">Авторизироваться</a></li>
                        <li><a href="/register" class="font-medium {{ request()->is('register') ? 'active' : '' }}">Зарегистрироваться</a></li>
                    @endauth

                    <li class="menu-title mt-3"><span>Правовая информация</span></li>
                    <li><a href="/privacy-policy" class="{{ request()->is('privacy-policy') ? 'active' : '' }}">Политика конфиденциальности</a></li>
                    <li><a href="/cookie-policy" class="{{ request()->is('cookie-policy') ? 'active' : '' }}">Политика cookie</a></li>

                    <li class="menu-title mt-3"><span>Настройки</span></li>
                    <li>
                        <label class="flex cursor-pointer items-center justify-between rounded-box px-4 py-2 hover:bg-base-200">
                            <span>Тёмная тема</span>
                            <input type="checkbox" class="toggle" data-theme-toggle aria-label="Переключить тёмную тему" />
                        </label>
                    </li>
                </ul>
            </aside>
        </div>
    </div>

    @livewireScripts
    <script>
        (function () {
            const cookieName = 'theme';
            const toggle = document.querySelector('[data-theme-toggle]');

            if (!toggle) {
                return;
            }

            const currentTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'cupcake';
            toggle.checked = currentTheme === 'dark';

            toggle.addEventListener('change', function () {
                const nextTheme = this.checked ? 'dark' : 'cupcake';

                document.documentElement.setAttribute('data-theme', nextTheme);
                document.cookie = cookieName + '=' + encodeURIComponent(nextTheme) + '; path=/; max-age=31536000; samesite=lax';
            });
        })();
    </script>
</body>
</html>
