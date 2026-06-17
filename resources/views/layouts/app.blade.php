@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="ru" class="group">
<head>
    @php
        $pageTitle = isset($title) ? trim($title) : trim($__env->yieldContent('title', config('app.name')));
        $metaDescription = trim($__env->yieldContent('meta_description', 'Платформа для команд, хакатонов и совместных проектов.'));
        $canonicalUrl = trim($__env->yieldContent('canonical_url', url()->current()));
        $ogTitle = trim($__env->yieldContent('og_title', $pageTitle));
        $ogDescription = trim($__env->yieldContent('og_description', $metaDescription));
        $ogImage = trim($__env->yieldContent('og_image', url('/logo.svg')));
        $robots = trim($__env->yieldContent('robots', 'index,follow'));
        $twitterCard = trim($__env->yieldContent('twitter_card', 'summary_large_image'));
    @endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">

    <meta name="twitter:card" content="{{ $twitterCard }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <title>{{ $pageTitle }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/logo.svg">
    <script>
        (function () {
            const cookieName = 'theme';
            const darkTheme = 'hackatonshik';
            const lightTheme = 'hackatonshik-light';
            const cookieMatch = document.cookie.match(new RegExp('(?:^|; )' + cookieName + '=([^;]*)'));
            const savedTheme = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme === darkTheme || savedTheme === lightTheme
                ? savedTheme
                : (systemPrefersDark ? darkTheme : lightTheme);

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
            <header class="navbar min-h-12 border-b border-base-200 bg-base-100 px-3 sm:min-h-14 sm:px-4 lg:hidden">
                <div class="flex w-full min-w-0 flex-1 items-center gap-2">
                    <label for="main-nav-drawer" class="btn btn-ghost btn-square min-h-12 min-w-12 shrink-0 drawer-button" aria-label="Открыть меню">
                        <x-app-icon icon="heroicons:bars-3" class="h-6 w-6" />
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

            <main id="main-content" class="flex-1 pb-[max(5rem,calc(4.5rem+env(safe-area-inset-bottom)))] lg:pb-0" tabindex="-1">
                <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-7 lg:px-8 lg:py-8">
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

            <nav
                class="btm-nav btm-nav-touch lg:hidden z-60 border-t border-base-200 bg-base-100 pb-[env(safe-area-inset-bottom)]"
                aria-label="Нижняя навигация"
            >
                <a href="{{ route('home') }}" wire:navigate @class([request()->routeIs('home') ? 'active text-primary' : 'text-base-content/75'])>
                    <x-app-icon icon="heroicons:home" class="h-6 w-6" />
                    <span class="btm-nav-label">Главная</span>
                </a>
                @auth
                    @php
                        $btmUser = Auth::user();
                        $btmStaff = $btmUser->isOrganizer() || $btmUser->isJudge() || $btmUser->isAdmin();
                    @endphp
                    @if ($btmStaff)
                        @if ($btmUser->isOrganizer())
                            <a href="{{ route('organizer.dashboard') }}" wire:navigate @class([request()->routeIs('organizer.dashboard', 'profile.organizer') ? 'active text-primary' : 'text-base-content/75'])>
                                <x-app-icon icon="heroicons:squares-2x2" class="h-6 w-6" />
                                <span class="btm-nav-label">Орг.</span>
                            </a>
                        @elseif ($btmUser->isJudge())
                            <a href="{{ route('judge.dashboard') }}" wire:navigate @class([request()->is('judge*') ? 'active text-primary' : 'text-base-content/75'])>
                                <x-app-icon icon="heroicons:scale" class="h-6 w-6" />
                                <span class="btm-nav-label">Судья</span>
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" wire:navigate @class([request()->is('admin*') ? 'active text-primary' : 'text-base-content/75'])>
                                <x-app-icon icon="heroicons:shield-check" class="h-6 w-6" />
                                <span class="btm-nav-label">Админ</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('teams.index') }}" wire:navigate @class([request()->is('teams*') ? 'active text-primary' : 'text-base-content/75'])>
                            <x-app-icon icon="heroicons:user-group" class="h-6 w-6" />
                            <span class="btm-nav-label">Команды</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('teams.index') }}" wire:navigate @class([request()->is('teams*') ? 'active text-primary' : 'text-base-content/75'])>
                        <x-app-icon icon="heroicons:user-group" class="h-6 w-6" />
                        <span class="btm-nav-label">Команды</span>
                    </a>
                @endauth
                <label for="main-nav-drawer" class="flex min-h-14 min-w-0 flex-1 cursor-pointer flex-col items-center justify-center gap-0.5 text-base-content/75">
                    <x-app-icon icon="heroicons:bars-3" class="h-6 w-6" />
                    <span class="btm-nav-label">Меню</span>
                </label>
                @guest
                    <a href="{{ route('hackatons.index') }}" wire:navigate @class([request()->is('hackatons*') ? 'active text-primary' : 'text-base-content/75'])>
                        <x-app-icon icon="heroicons:rocket-launch" class="h-6 w-6" />
                        <span class="btm-nav-label">Хакатоны</span>
                    </a>
                @else
                    @if ($btmStaff)
                        @if ($btmUser->isOrganizer())
                            <a href="{{ route('organizer.applications') }}" wire:navigate @class([request()->routeIs('organizer.applications', 'profile.hackatons.applications') ? 'active text-primary' : 'text-base-content/75'])>
                                <x-app-icon icon="heroicons:rocket-launch" class="h-6 w-6" />
                                <span class="btm-nav-label">Заявки</span>
                            </a>
                        @elseif ($btmUser->isJudge())
                            <a href="{{ route('judge.dashboard') }}" wire:navigate @class([request()->is('judge*') ? 'active text-primary' : 'text-base-content/75'])>
                                <x-app-icon icon="heroicons:clipboard-document-list" class="h-6 w-6" />
                                <span class="btm-nav-label">Хакатоны</span>
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" wire:navigate @class([request()->is('admin*') ? 'active text-primary' : 'text-base-content/75'])>
                                <x-app-icon icon="heroicons:chart-bar-square" class="h-6 w-6" />
                                <span class="btm-nav-label">Админ</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('hackatons.index') }}" wire:navigate @class([request()->is('hackatons*') ? 'active text-primary' : 'text-base-content/75'])>
                            <x-app-icon icon="heroicons:rocket-launch" class="h-6 w-6" />
                            <span class="btm-nav-label">Хакатоны</span>
                        </a>
                    @endif
                @endguest
                @auth
                    <a href="{{ route('profile') }}" wire:navigate @class([request()->is('profile*') ? 'active text-primary' : 'text-base-content/75'])>
                        <x-app-icon icon="heroicons:user-circle" class="h-6 w-6" />
                        <span class="btm-nav-label">Профиль</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate @class([request()->is('login') ? 'active text-primary' : 'text-base-content/75'])>
                        <x-app-icon icon="heroicons:arrow-right-on-rectangle" class="h-6 w-6" />
                        <span class="btm-nav-label">Войти</span>
                    </a>
                @endauth
            </nav>
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
                        <div class="flex w-full items-center gap-2">
                            <div class="dropdown dropdown-end dropdown-bottom ml-auto">
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
                            <div class="dropdown dropdown-start order-first">
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
                    @guest
                        <li class="menu-title px-1 pt-1"><span class="font-display text-xs font-medium text-base-content/55">Навигация</span></li>
                        <li>
                            <a href="{{ route('home') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                <x-app-icon icon="heroicons:home" class="h-5 w-5 shrink-0" />
                                <span class="min-w-0 flex-1">Главная</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hackatons.index') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('hackatons.index', 'hackatons.show') || request()->is('hackatons/*') ? 'active' : '' }}">
                                <x-app-icon icon="heroicons:rocket-launch" class="h-5 w-5 shrink-0" />
                                <span class="min-w-0 flex-1">Хакатоны</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('teams.index') }}" wire:navigate class="sidebar-nav-link {{ request()->is('teams*') ? 'active' : '' }}">
                                <x-app-icon icon="heroicons:user-group" class="h-5 w-5 shrink-0" />
                                <span class="min-w-0 flex-1">Команды</span>
                            </a>
                        </li>
                        <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>
                        <li class="menu-title px-1"><span class="font-display text-xs font-medium text-base-content/55">Аккаунт</span></li>
                        <li>
                            <a href="{{ route('login') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('login') ? 'active' : '' }}">
                                <x-app-icon icon="heroicons:arrow-right-on-rectangle" class="h-5 w-5 shrink-0" />
                                <span class="min-w-0 flex-1">Войти</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('register') ? 'active' : '' }}">
                                <x-app-icon icon="heroicons:user-plus" class="h-5 w-5 shrink-0" />
                                <span class="min-w-0 flex-1">Регистрация</span>
                            </a>
                        </li>
                    @else
                        @php
                            $navUser = Auth::user();
                            $isPureParticipant = ! $navUser->isOrganizer() && ! $navUser->isJudge() && ! $navUser->isAdmin();
                        @endphp

                        @if ($isPureParticipant)
                            <li class="menu-title px-1 pt-1"><span class="font-display text-xs font-medium text-base-content/55">Навигация</span></li>
                            <li>
                                <a href="{{ route('home') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:home" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Главная</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('hackatons.index') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('hackatons.index', 'hackatons.show') || request()->is('hackatons/*') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:rocket-launch" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Хакатоны</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('teams.index') }}" wire:navigate class="sidebar-nav-link {{ request()->is('teams*') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:user-group" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Команды</span>
                                </a>
                            </li>
                            <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>
                            <li class="menu-title px-1"><span class="font-display text-xs font-medium text-base-content/55">Участие</span></li>
                            <li>
                                <a href="{{ route('profile.teams') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('profile.teams') || request()->is('profile/teams*') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:users" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Мои команды</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('teams.create') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('teams.create') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:plus-circle" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Создать команду</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('participant.hackatons') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('participant.hackatons', 'participant.hackatons.hub') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:rectangle-stack" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Мои заявки и хакатоны</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.certificates') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('profile.certificates') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:academic-cap" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Сертификаты</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:user-circle" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Профиль</span>
                                </a>
                            </li>
                        @else
                            <li class="menu-title px-1 pt-1"><span class="font-display text-xs font-medium text-base-content/55">Навигация</span></li>
                            <li>
                                <a href="{{ route('home') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:home" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Главная</span>
                                </a>
                            </li>
                            @if ($navUser->isOrganizer() || $navUser->isJudge())
                                <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>
                                <li class="menu-title px-1"><span class="font-display text-xs font-medium text-base-content/55">Хакатоны</span></li>
                                @if ($navUser->isOrganizer())
                                    <li>
                                        <a href="{{ route('hackatons.create') }}" wire:navigate class="sidebar-nav-link sidebar-nav-link--partner-cta {{ request()->routeIs('hackatons.create') ? 'active' : '' }}">
                                            <x-app-icon icon="heroicons:plus" class="h-5 w-5 shrink-0" />
                                            <span class="min-w-0 flex-1">Создать новый хакатон</span>
                                        </a>
                                    </li>
                                @elseif ($navUser->isJudge())
                                    <li>
                                        <a href="{{ route('judge.dashboard') }}" wire:navigate class="sidebar-nav-link {{ request()->is('judge*') ? 'active' : '' }}">
                                            <x-app-icon icon="heroicons:clipboard-document-list" class="h-5 w-5 shrink-0" />
                                            <span class="min-w-0 flex-1">Назначенные хакатоны</span>
                                        </a>
                                    </li>
                                @endif
                            @endif
                            <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>
                            <li class="menu-title px-1"><span class="font-display text-xs font-medium text-base-content/55">Кабинет</span></li>
                            @if ($navUser->isOrganizer())
                                <li>
                                    <a href="{{ route('organizer.dashboard') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('organizer.dashboard', 'profile.hackatons', 'profile.organizer', 'organizer.applications', 'organizer.scoring', 'organizer.finished', 'organizer.participants') ? 'active' : '' }}">
                                        <x-app-icon icon="heroicons:squares-2x2" class="h-5 w-5 shrink-0" />
                                        <span class="min-w-0 flex-1">Организаторский кабинет</span>
                                        @if (isset($partnerSidebarCounts) && $partnerSidebarCounts !== null && $partnerSidebarCounts->totalHackatonsCount > 0)
                                            <span class="badge badge-sm badge-ghost shrink-0 tabular-nums" title="Активные / всего">
                                                {{ $partnerSidebarCounts->activeHackatonsCount }}/{{ $partnerSidebarCounts->totalHackatonsCount }}
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @elseif ($navUser->isAdmin())
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="sidebar-nav-link {{ request()->is('admin*') ? 'active' : '' }}">
                                        <x-app-icon icon="heroicons:shield-check" class="h-5 w-5 shrink-0" />
                                        <span class="min-w-0 flex-1">Админ-панель</span>
                                    </a>
                                </li>
                            @endif
                            @if ($navUser->isJudge())
                                <li>
                                    <a href="{{ route('judge.dashboard') }}" wire:navigate class="sidebar-nav-link {{ request()->is('judge*') ? 'active' : '' }}">
                                        <x-app-icon icon="heroicons:scale" class="h-5 w-5 shrink-0" />
                                        <span class="min-w-0 flex-1">Панель судьи</span>
                                    </a>
                                </li>
                            @endif
                            @if ($navUser->isOrganizer() && isset($partnerSidebarCounts) && $partnerSidebarCounts !== null)
                                <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>
                                <li class="menu-title px-1"><span class="font-display text-xs font-medium text-base-content/55">Быстрые действия</span></li>
                                <li>
                                    <a href="{{ route('organizer.applications') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('organizer.applications', 'profile.hackatons.applications') ? 'active' : '' }}">
                                        <x-app-icon icon="heroicons:inbox" class="h-5 w-5 shrink-0" />
                                        <span class="min-w-0 flex-1">Заявки на рассмотрении</span>
                                        @if ($partnerSidebarCounts->pendingApplicationsCount > 0)
                                            <span class="badge badge-sm badge-error shrink-0 tabular-nums">{{ min($partnerSidebarCounts->pendingApplicationsCount, 99) }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('organizer.scoring') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('organizer.scoring', 'profile.hackatons.scoring') ? 'active' : '' }}">
                                        <x-app-icon icon="heroicons:clipboard-document-check" class="h-5 w-5 shrink-0" />
                                        <span class="min-w-0 flex-1">Сводка по оценкам</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('organizer.finished') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('organizer.finished', 'profile.hackatons.finished') ? 'active' : '' }}">
                                        <x-app-icon icon="heroicons:archive-box" class="h-5 w-5 shrink-0" />
                                        <span class="min-w-0 flex-1">Завершённые хакатоны</span>
                                    </a>
                                </li>
                            @endif
                            <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>
                            <li>
                                <a href="{{ route('profile') }}" wire:navigate class="sidebar-nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                                    <x-app-icon icon="heroicons:user-circle" class="h-5 w-5 shrink-0" />
                                    <span class="min-w-0 flex-1">Профиль</span>
                                </a>
                            </li>
                        @endif
                    @endguest

                    <li role="presentation" class="sidebar-nav-divider list-none px-2 py-0"><div class="divider my-0 border-base-300/50"></div></li>

                    <li class="menu-title px-1"><span class="font-display text-xs font-medium text-base-content/55">Настройки</span></li>
                    <li>
                        <label class="sidebar-theme-toggle flex cursor-pointer items-center justify-between gap-3 rounded-xl border-l-4 border-transparent px-3 py-3 text-sm font-medium leading-snug text-base-content transition-colors duration-200 hover:border-primary/25 hover:bg-base-200/85">
                            <span class="text-[0.9375rem]">Тёмная тема</span>
                            <input
                                type="checkbox"
                                class="toggle toggle-primary shrink-0"
                                data-theme-toggle
                                role="switch"
                                aria-label="Переключить тёмную тему"
                                aria-checked="false"
                            />
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
            const darkTheme = 'hackatonshik';
            const lightTheme = 'hackatonshik-light';
            const toggle = document.querySelector('[data-theme-toggle]');

            if (!toggle) {
                return;
            }

            const currentTheme =
                document.documentElement.getAttribute('data-theme') === lightTheme
                    ? lightTheme
                    : darkTheme;
            toggle.checked = currentTheme === darkTheme;
            toggle.setAttribute('aria-checked', toggle.checked ? 'true' : 'false');

            toggle.addEventListener('change', function () {
                const nextTheme = this.checked ? darkTheme : lightTheme;

                document.documentElement.setAttribute('data-theme', nextTheme);
                document.cookie = cookieName + '=' + encodeURIComponent(nextTheme) + '; path=/; max-age=31536000; samesite=lax';
                this.setAttribute('aria-checked', this.checked ? 'true' : 'false');
            });
        })();
    </script>
</body>
</html>
