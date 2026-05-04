@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="ru" class="group">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Платформа для команд, хакатонов и совместных проектов.')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', config('app.name'))))">
    <meta property="og:description" content="@yield('og_description', $__env->yieldContent('meta_description', 'Платформа для команд, хакатонов и совместных проектов.'))">
    <meta property="og:url" content="@yield('canonical_url', url()->current())">
    <meta property="og:image" content="@yield('og_image', url('/logo.svg'))">
    <title>
        @isset($title)
            {{ $title }}
        @else
            @yield('title', config('app.name'))
        @endisset
    </title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/logo.svg">
    <script>
        (function () {
            const cookieName = 'theme';
            const cookieMatch = document.cookie.match(new RegExp('(?:^|; )' + cookieName + '=([^;]*)'));
            const savedTheme = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
            const theme =
                savedTheme === 'hackatonshik-light' || savedTheme === 'cupcake'
                    ? 'hackatonshik-light'
                    : 'hackatonshik';

            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen overflow-x-hidden bg-base-300 font-sans antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-100 focus:rounded-lg focus:bg-base-100 focus:px-4 focus:py-2 focus:shadow">
        Перейти к основному контенту
    </a>
    <div class="drawer lg:drawer-open min-h-screen">
        <input id="main-nav-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex min-h-screen flex-col">
            <header class="navbar min-h-12 border-b border-base-200 bg-base-100/95 px-3 shadow-sm backdrop-blur-sm sm:min-h-14 sm:px-4 lg:hidden">
                <div class="flex w-full min-w-0 flex-1 items-center gap-2">
                    <label for="main-nav-drawer" class="btn btn-ghost btn-circle drawer-button shrink-0" aria-label="Открыть меню">
                        <x-app-icon icon="heroicons:bars-3" class="h-5 w-5" />
                    </label>
                    <div class="flex h-10 min-h-0 min-w-0 flex-1 items-stretch sm:h-11">
                        <x-app-brand
                            :wide="true"
                            class="h-full min-h-0 min-w-0 border-0 p-0 hover:bg-transparent"
                            img-class="h-full w-full object-cover object-left"
                        />
                    </div>
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

            <footer class="footer sm:footer-horizontal mt-auto border-t border-primary/15 bg-base-200 text-base-content px-6 py-8 sm:px-10 sm:py-10 gap-6">
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
            <aside
                id="app-sidebar"
                class="flex min-h-full w-80 max-h-[100dvh] flex-col overflow-x-visible overflow-y-auto overscroll-y-contain border-r border-base-200 bg-base-100 bg-linear-to-b from-base-100 to-base-200/50 p-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] sm:p-6 lg:max-h-none lg:overflow-y-visible lg:pb-6"
                aria-label="Основная навигация"
            >
                <div class="relative flex shrink-0 flex-col gap-3">
                    <div class="w-full rounded-2xl border border-base-300/35 bg-base-200/45 p-3 ring-1 ring-base-content/[0.06]">
                        <x-app-brand
                            :wide="true"
                            class="min-h-0 w-full border-0 p-0 hover:bg-transparent"
                            img-class="h-auto w-full min-h-0 object-contain object-left"
                        />
                    </div>
                    @auth
                        @php
                            $unreadNotificationsCount = Auth::user()->unreadNotifications()->count();
                            $recentNotifications = Auth::user()->notifications()->latest()->limit(5)->get();
                        @endphp
                        <div class="flex justify-end gap-2">
                            <div class="dropdown dropdown-end dropdown-bottom">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-circle" aria-label="Уведомления">
                                    <div class="indicator">
                                        <x-app-icon icon="heroicons:bell" class="h-5 w-5" />
                                        @if($unreadNotificationsCount > 0)
                                            <span class="badge badge-xs badge-error indicator-item">{{ min($unreadNotificationsCount, 9) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div tabindex="-1" class="card card-compact dropdown-content z-50 mt-3 w-[min(20rem,calc(100vw-2rem))] max-w-[min(20rem,calc(100vw-2rem))] border border-base-200 bg-base-100 shadow-xl">
                                    <div class="card-body gap-2">
                                        <div class="flex items-center justify-between">
                                            <p class="font-medium">Уведомления</p>
                                            @if($unreadNotificationsCount > 0)
                                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-ghost btn-xs">Прочитать все</button>
                                                </form>
                                            @endif
                                        </div>
                                        @if($recentNotifications->isEmpty())
                                            <p class="text-sm text-base-content/70">Пока нет уведомлений.</p>
                                        @else
                                            <div class="space-y-2">
                                                @foreach($recentNotifications as $notification)
                                                    @php
                                                        $notificationData = $notification->data ?? [];
                                                        $notificationUrl = $notificationData['url'] ?? route('home');
                                                        $notificationTitle = $notificationData['title'] ?? 'Новое уведомление';
                                                        $notificationMessage = $notificationData['message'] ?? null;
                                                    @endphp
                                                    <div class="rounded-lg border border-base-200 p-2 {{ $notification->read_at ? 'opacity-80' : 'bg-base-200/40' }}">
                                                        <a href="{{ $notificationUrl }}" class="block">
                                                            <p class="text-sm font-medium">{{ $notificationTitle }}</p>
                                                            @if(filled($notificationMessage))
                                                                <p class="text-xs text-base-content/70">{{ $notificationMessage }}</p>
                                                            @endif
                                                        </a>
                                                        @if($notification->read_at === null)
                                                            <form method="POST" action="{{ route('notifications.read', $notification) }}" class="mt-1">
                                                                @csrf
                                                                <button type="submit" class="btn btn-ghost btn-xs px-1">Отметить прочитанным</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                                    <div class="w-10 rounded-full">
                                        <img alt="Аватар пользователя" src="{{ Auth::user()?->avatar_path ? asset('storage/'.Auth::user()->avatar_path) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->fio ?? 'U').'&background=random' }}" />
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
                        </div>
                    @endauth
                </div>

                <ul class="menu menu-vertical app-sidebar-menu mt-5 w-full min-w-0 flex-1 gap-0.5 px-0 py-1" role="navigation" aria-label="Меню сайта">
                    <li class="menu-title px-1 pt-1"><span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.14em] text-base-content/60">Основное</span></li>
                    <li><a href="/teams" class="sidebar-nav-link {{ request()->is('teams*') ? 'active' : '' }}"><x-app-icon icon="heroicons:user-group" class="h-5 w-5" />Команды</a></li>
                    <li><a href="/hackatons" class="sidebar-nav-link {{ request()->is('hackatons*') ? 'active' : '' }}"><x-app-icon icon="heroicons:rocket-launch" class="h-5 w-5" />Хакатоны</a></li>

                    <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>

                    <li class="menu-title px-1"><span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.14em] text-base-content/60">Мой кабинет</span></li>
                    @auth
                        @if (Auth::user()->role === 'user')
                            <li><a href="/profile/teams" class="sidebar-nav-link {{ request()->is('profile/teams*') ? 'active' : '' }}"><x-app-icon icon="heroicons:user-group" class="h-5 w-5" />Мои команды</a></li>
                            <li><a href="/teams/create" class="sidebar-nav-link {{ request()->is('teams/create') ? 'active' : '' }}"><x-app-icon icon="heroicons:plus-circle" class="h-5 w-5" />Создать команду</a></li>
                            <li><a href="/profile/certificates" class="sidebar-nav-link {{ request()->is('profile/certificates') ? 'active' : '' }}"><x-app-icon icon="heroicons:academic-cap" class="h-5 w-5" />Сертификаты</a></li>
                        @elseif (Auth::user()->role === 'partner')
                            <li><a href="/profile/hackatons" class="sidebar-nav-link {{ request()->is('profile/hackatons*') ? 'active' : '' }}"><x-app-icon icon="heroicons:clipboard-document-list" class="h-5 w-5" />Мои хакатоны</a></li>
                            <li><a href="/hackatons/create" class="sidebar-nav-link {{ request()->is('hackatons/create') ? 'active' : '' }}"><x-app-icon icon="heroicons:plus-circle" class="h-5 w-5" />Создать хакатон</a></li>
                        @elseif (Auth::user()->role === 'judge')
                            <li><a href="/hackatons" class="sidebar-nav-link {{ request()->is('hackatons*') ? 'active' : '' }}"><x-app-icon icon="heroicons:scale" class="h-5 w-5" />Назначенные хакатоны</a></li>
                        @elseif (Auth::user()->role === 'admin')
                            <li><a href="/admin" class="sidebar-nav-link {{ request()->is('admin') ? 'active' : '' }}"><x-app-icon icon="heroicons:chart-bar-square" class="h-5 w-5" />Админ-панель</a></li>
                        @endif
                        <li><a href="/profile" class="sidebar-nav-link {{ request()->is('profile') ? 'active' : '' }}"><x-app-icon icon="heroicons:user-circle" class="h-5 w-5" />Профиль</a></li>
                    @else
                        <li><a href="/login" class="sidebar-nav-link {{ request()->is('login') ? 'active' : '' }}"><x-app-icon icon="heroicons:arrow-right-on-rectangle" class="h-5 w-5" />Авторизироваться</a></li>
                        <li><a href="/register" class="sidebar-nav-link {{ request()->is('register') ? 'active' : '' }}"><x-app-icon icon="heroicons:user-plus" class="h-5 w-5" />Зарегистрироваться</a></li>
                    @endauth

                    <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>

                    <li class="menu-title px-1"><span class="font-display text-[0.7rem] font-semibold uppercase tracking-[0.14em] text-base-content/60">Настройки</span></li>
                    <li>
                        <label class="sidebar-theme-toggle flex cursor-pointer items-center justify-between gap-3 rounded-xl border-l-4 border-transparent px-3 py-3 text-sm font-medium leading-snug text-base-content transition-colors duration-200 hover:border-primary/25 hover:bg-base-200/85">
                            <span class="text-[0.9375rem]">Тёмная тема</span>
                            <input type="checkbox" class="toggle toggle-primary shrink-0" data-theme-toggle aria-label="Переключить тёмную тему" />
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

            const currentTheme =
                document.documentElement.getAttribute('data-theme') === 'hackatonshik-light'
                    ? 'hackatonshik-light'
                    : 'hackatonshik';
            toggle.checked = currentTheme === 'hackatonshik';

            toggle.addEventListener('change', function () {
                const nextTheme = this.checked ? 'hackatonshik' : 'hackatonshik-light';

                document.documentElement.setAttribute('data-theme', nextTheme);
                document.cookie = cookieName + '=' + encodeURIComponent(nextTheme) + '; path=/; max-age=31536000; samesite=lax';
            });
        })();
    </script>
</body>
</html>
