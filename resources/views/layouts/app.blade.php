@php use App\Support\ThemeResolver; use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
@php
    $serverTheme = ThemeResolver::fromCookie(request()->cookie('theme'));
    $darkTheme = config('theme.dark');
    $lightTheme = config('theme.light');
    $legacyThemes = config('theme.legacy');
@endphp
<html lang="ru" class="group" data-theme="{{ $serverTheme }}">
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
            const darkTheme = @json($darkTheme);
            const lightTheme = @json($lightTheme);
            const legacyThemes = @json($legacyThemes);

            function resolveTheme(saved) {
                if (saved === darkTheme || saved === lightTheme) {
                    return saved;
                }

                if (saved && legacyThemes[saved]) {
                    return legacyThemes[saved];
                }

                return window.matchMedia('(prefers-color-scheme: dark)').matches
                    ? darkTheme
                    : lightTheme;
            }

            const cookieMatch = document.cookie.match(new RegExp('(?:^|; )' + cookieName + '=([^;]*)'));
            const savedTheme = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;

            document.documentElement.setAttribute('data-theme', resolveTheme(savedTheme));
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
                    @auth
                        <livewire:organizer-application-modal />
                    @endauth
                    @hasSection('slot')
                        @yield('slot')
                    @elseif (isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </div>
            </main>

            <footer class="footer sm:footer-horizontal mt-auto border-t border-base-300 bg-base-200 text-base-content px-6 py-8 sm:px-10 sm:py-10 gap-6">
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

            {{-- Cookie consent banner (152-ФЗ / ePrivacy) --}}
            <div
                x-data="{ visible: !document.cookie.split(';').some(c => c.trim().startsWith('cookie_consent=')) }"
                x-show="visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="translate-y-full opacity-0"
                class="fixed bottom-[max(5rem,calc(4.5rem+env(safe-area-inset-bottom)))] lg:bottom-0 left-0 right-0 z-50 border-t border-base-300 bg-base-100 shadow-lg"
            >
                <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:px-6 lg:px-8">
                    <p class="text-sm text-base-content/80">
                        Мы используем файлы cookie для корректной работы сервиса и аналитики.
                        Продолжая пользоваться сайтом, вы соглашаетесь с нашей
                        <a href="/cookie-policy" class="link link-primary">Политикой куки</a> и
                        <a href="/privacy-policy" class="link link-primary">Политикой конфиденциальности</a>.
                    </p>
                    <div class="flex shrink-0 gap-2">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            @click="document.cookie='cookie_consent=accepted;path=/;max-age=31536000;SameSite=Lax'; visible=false"
                        >
                            Принять
                        </button>
                        <a href="/cookie-policy" class="btn btn-ghost btn-sm">Подробнее</a>
                    </div>
                </div>
            </div>

            <nav
                class="btm-nav btm-nav-touch lg:hidden z-60 border-t border-base-200 bg-base-100 pb-[env(safe-area-inset-bottom)]"
                aria-label="Нижняя навигация"
            >
                <a href="{{ route('home') }}" wire:navigate @class([request()->routeIs('home') ? 'active text-primary' : 'text-base-content/70'])>
                    <x-app-icon icon="heroicons:home" class="h-6 w-6" />
                    <span class="btm-nav-label">Главная</span>
                </a>
                @auth
                    @php
                        $btmUser = Auth::user();
                        $btmStaff = $btmUser->isOrganizer() || $btmUser->isJudge() || $btmUser->isAdmin() || $btmUser->isModerator();
                    @endphp
                    @if ($btmStaff)
                        @if ($btmUser->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" wire:navigate @class([request()->is('admin*') ? 'active text-primary' : 'text-base-content/70'])>
                                <x-app-icon icon="heroicons:shield-check" class="h-6 w-6" />
                                <span class="btm-nav-label">Админ</span>
                            </a>
                        @elseif ($btmUser->isOrganizer())
                            <a href="{{ route('organizer.dashboard') }}" wire:navigate @class([request()->routeIs('organizer.dashboard', 'profile.organizer') ? 'active text-primary' : 'text-base-content/70'])>
                                <x-app-icon icon="heroicons:squares-2x2" class="h-6 w-6" />
                                <span class="btm-nav-label">Орг.</span>
                            </a>
                        @elseif ($btmUser->isJudge())
                            <a href="{{ route('judge.dashboard') }}" wire:navigate @class([request()->is('judge*') ? 'active text-primary' : 'text-base-content/70'])>
                                <x-app-icon icon="heroicons:scale" class="h-6 w-6" />
                                <span class="btm-nav-label">Судья</span>
                            </a>
                        @elseif ($btmUser->isModerator())
                            <a href="{{ route('admin.dashboard') }}" wire:navigate @class([request()->is('admin*') ? 'active text-primary' : 'text-base-content/70'])>
                                <x-app-icon icon="heroicons:shield-exclamation" class="h-6 w-6" />
                                <span class="btm-nav-label">Модер.</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('teams.index') }}" wire:navigate @class([request()->is('teams*') ? 'active text-primary' : 'text-base-content/70'])>
                            <x-app-icon icon="heroicons:user-group" class="h-6 w-6" />
                            <span class="btm-nav-label">Команды</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('teams.index') }}" wire:navigate @class([request()->is('teams*') ? 'active text-primary' : 'text-base-content/70'])>
                        <x-app-icon icon="heroicons:user-group" class="h-6 w-6" />
                        <span class="btm-nav-label">Команды</span>
                    </a>
                @endauth
                <label for="main-nav-drawer" class="flex min-h-14 min-w-0 flex-1 cursor-pointer flex-col items-center justify-center gap-0.5 text-base-content/70">
                    <x-app-icon icon="heroicons:bars-3" class="h-6 w-6" />
                    <span class="btm-nav-label">Меню</span>
                </label>
                @guest
                    <a href="{{ route('hackatons.index') }}" wire:navigate @class([request()->is('hackatons*') ? 'active text-primary' : 'text-base-content/70'])>
                        <x-app-icon icon="heroicons:rocket-launch" class="h-6 w-6" />
                        <span class="btm-nav-label">Хакатоны</span>
                    </a>
                @else
                    @if ($btmStaff)
                        @if ($btmUser->isAdmin())
                            <a href="{{ route('hackatons.index') }}" wire:navigate @class([request()->is('hackatons*') ? 'active text-primary' : 'text-base-content/70'])>
                                <x-app-icon icon="heroicons:rocket-launch" class="h-6 w-6" />
                                <span class="btm-nav-label">Хакатоны</span>
                            </a>
                        @elseif ($btmUser->isOrganizer())
                            <a href="{{ route('organizer.applications') }}" wire:navigate @class([request()->routeIs('organizer.applications', 'profile.hackatons.applications') ? 'active text-primary' : 'text-base-content/70'])>
                                <x-app-icon icon="heroicons:rocket-launch" class="h-6 w-6" />
                                <span class="btm-nav-label">Заявки</span>
                            </a>
                        @elseif ($btmUser->isJudge())
                            <a href="{{ route('judge.dashboard') }}" wire:navigate @class([request()->is('judge*') ? 'active text-primary' : 'text-base-content/70'])>
                                <x-app-icon icon="heroicons:clipboard-document-list" class="h-6 w-6" />
                                <span class="btm-nav-label">Хакатоны</span>
                            </a>
                        @elseif ($btmUser->isModerator())
                            <a href="{{ route('hackatons.index') }}" wire:navigate @class([request()->is('hackatons*') ? 'active text-primary' : 'text-base-content/70'])>
                                <x-app-icon icon="heroicons:rocket-launch" class="h-6 w-6" />
                                <span class="btm-nav-label">Хакатоны</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('hackatons.index') }}" wire:navigate @class([request()->is('hackatons*') ? 'active text-primary' : 'text-base-content/70'])>
                            <x-app-icon icon="heroicons:rocket-launch" class="h-6 w-6" />
                            <span class="btm-nav-label">Хакатоны</span>
                        </a>
                    @endif
                @endguest
                @auth
                    <a href="{{ route('profile') }}" wire:navigate @class([request()->is('profile*') ? 'active text-primary' : 'text-base-content/70'])>
                        <x-app-icon icon="heroicons:user-circle" class="h-6 w-6" />
                        <span class="btm-nav-label">Профиль</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate @class([request()->is('login') ? 'active text-primary' : 'text-base-content/70'])>
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
                class="flex min-h-full w-80 max-h-[100dvh] flex-col overflow-hidden border-r border-base-200 bg-base-100 bg-linear-to-b from-base-100 to-base-200/50 p-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] sm:p-6 lg:max-h-none lg:overflow-visible lg:pb-6"
                aria-label="Основная навигация"
            >
                <div class="relative flex shrink-0 flex-col gap-3 overflow-visible">
                    <div class="px-1 py-2">
                        <x-app-brand
                            :wide="true"
                            class="min-h-0 w-full border-0 p-0 hover:bg-transparent"
                            img-class="h-auto w-full min-h-0 object-contain object-left"
                        />
                    </div>
                    <livewire:global-search />
                    @auth
                        <div class="flex w-full items-center gap-2">
                            <div class="ml-auto">
                                <livewire:notification-bell />
                            </div>
                            <div class="dropdown dropdown-start order-first">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                                    <div class="w-10 rounded-full">
                                        <img alt="Аватар пользователя" src="{{ Auth::user()?->avatar_path ? asset('storage/'.Auth::user()->avatar_path) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->fio ?? 'U').'&background=random' }}" />
                                    </div>
                                </div>
                                <ul tabindex="-1" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-50 mt-3 w-52 p-2 shadow">
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

                <x-mary-menu activate-by-route class="mt-5 w-full min-h-0 flex-1 overflow-y-auto overscroll-y-contain px-0 lg:overflow-y-visible" role="navigation" aria-label="Меню сайта">
                    @guest
                        <x-mary-menu-item title="{{ __('ui.nav.home') }}" icon="o-home" link="{{ route('home') }}" exact />
                        <x-mary-menu-item title="{{ __('ui.nav.hackatons') }}" icon="o-rocket-launch" link="{{ route('hackatons.index') }}" />
                        <x-mary-menu-item title="{{ __('ui.nav.teams') }}" icon="o-user-group" link="{{ route('teams.index') }}" />
                        <x-marymenu-separator />
                        <x-mary-menu-item title="{{ __('ui.nav.login') }}" icon="o-arrow-right-on-rectangle" link="{{ route('login') }}" />
                        <x-mary-menu-item title="{{ __('ui.nav.register') }}" icon="o-user-plus" link="{{ route('register') }}" />
                    @else
                        @php $navUser = Auth::user(); $isPureParticipant = $navUser->canParticipate(); @endphp

                        @if ($isPureParticipant)
                            <x-mary-menu-item title="{{ __('ui.nav.home') }}" icon="o-home" link="{{ route('home') }}" exact />
                            <x-mary-menu-item title="{{ __('ui.nav.hackatons') }}" icon="o-rocket-launch" link="{{ route('hackatons.index') }}" />
                            <x-mary-menu-item title="{{ __('ui.nav.teams') }}" icon="o-user-group" link="{{ route('teams.index') }}" />
                            <x-marymenu-separator />
                            <x-mary-menu-item title="{{ __('ui.nav.my_teams') }}" icon="o-users" link="{{ route('profile.teams') }}" />
                            <x-mary-menu-item title="{{ __('ui.nav.create_team') }}" icon="o-plus-circle" link="{{ route('teams.create') }}" />
                            <x-mary-menu-item title="{{ __('ui.nav.my_hackatons') }}" icon="o-rectangle-stack" link="{{ route('participant.hackatons') }}" />
                            <x-mary-menu-item title="{{ __('ui.nav.certificates') }}" icon="o-academic-cap" link="{{ route('profile.certificates') }}" />
                            <x-marymenu-separator />
                            <x-mary-menu-item title="{{ __('ui.nav.profile') }}" icon="o-user-circle" link="{{ route('profile') }}" />
                        @else
                            <x-mary-menu-item title="{{ __('ui.nav.home') }}" icon="o-home" link="{{ route('home') }}" exact />
                            <x-mary-menu-item title="{{ __('ui.nav.hackatons') }}" icon="o-rocket-launch" link="{{ route('hackatons.index') }}" />
                            @if ($navUser->isOrganizer() || $navUser->isJudge())
                                <x-marymenu-separator />
                                @if ($navUser->isOrganizer())
                                    <x-mary-menu-item title="{{ __('ui.nav.create_hackaton') }}" icon="o-plus" link="{{ route('hackatons.create') }}" />
                                @elseif ($navUser->isJudge())
                                    <x-mary-menu-item title="{{ __('ui.nav.assigned_hackatons') }}" icon="o-clipboard-document-list" link="{{ route('judge.dashboard') }}" />
                                @endif
                            @endif
                            <x-marymenu-separator />
                            @if ($navUser->isOrganizer())
                                <x-mary-menu-item
                                    title="{{ __('ui.nav.organizer_cabinet') }}"
                                    icon="o-squares-2x2"
                                    link="{{ route('organizer.dashboard') }}"
                                    :active="request()->routeIs('organizer.dashboard', 'profile.hackatons', 'profile.organizer', 'organizer.applications', 'organizer.scoring', 'organizer.finished', 'organizer.participants')"
                                    :badge="isset($partnerSidebarCounts) && $partnerSidebarCounts?->totalHackatonsCount > 0 ? $partnerSidebarCounts->activeHackatonsCount.'/'.$partnerSidebarCounts->totalHackatonsCount : null"
                                />
                            @elseif ($navUser->isAdmin())
                                <x-mary-menu-item title="{{ __('ui.nav.admin_panel') }}" icon="o-shield-check" link="{{ route('admin.dashboard') }}" />
                            @elseif ($navUser->isModerator())
                                <x-mary-menu-item title="{{ __('ui.nav.moderator_panel') }}" icon="o-shield-exclamation" link="{{ route('admin.dashboard') }}" />
                            @endif
                            @if ($navUser->isJudge())
                                <x-mary-menu-item title="{{ __('ui.nav.judge_panel') }}" icon="o-scale" link="{{ route('judge.dashboard') }}" />
                            @endif
                            @if ($navUser->isOrganizer() && isset($partnerSidebarCounts) && $partnerSidebarCounts !== null)
                                <x-marymenu-separator />
                                <x-mary-menu-item
                                    title="{{ __('ui.nav.pending_applications') }}"
                                    icon="o-inbox"
                                    link="{{ route('organizer.applications') }}"
                                    :badge="$partnerSidebarCounts->pendingApplicationsCount > 0 ? min($partnerSidebarCounts->pendingApplicationsCount, 99) : null"
                                    badge-classes="badge-error"
                                />
                                <x-mary-menu-item title="{{ __('ui.nav.scoring_summary') }}" icon="o-clipboard-document-check" link="{{ route('organizer.scoring') }}" />
                                <x-mary-menu-item title="{{ __('ui.nav.finished_hackatons') }}" icon="o-archive-box" link="{{ route('organizer.finished') }}" />
                            @endif
                            <x-marymenu-separator />
                            <x-mary-menu-item title="{{ __('ui.nav.profile') }}" icon="o-user-circle" link="{{ route('profile') }}" />
                        @endif
                    @endguest
                </x-mary-menu>

                <div class="mt-2 shrink-0 border-t border-base-200 pt-2">
                    <label class="sidebar-theme-toggle flex cursor-pointer items-center justify-between gap-3 rounded-xl px-3 py-3 text-sm font-medium leading-snug text-base-content transition-colors duration-200 hover:bg-base-200">
                        <span class="text-[0.9375rem]">{{ __('ui.nav.dark_theme') }}</span>
                        <input
                            type="checkbox"
                            class="toggle toggle-primary shrink-0"
                            data-theme-toggle
                            role="switch"
                            aria-label="Переключить тёмную тему"
                            aria-checked="false"
                        />
                    </label>
                    <livewire:locale-switcher />
                </div>
            </aside>
        </div>
    </div>

    <x-marytoast position="toast-top toast-end" />
    <x-flash-toast-bridge />

    @livewireScripts
    <script>
        (function () {
            const darkTheme = @json($darkTheme);
            const lightTheme = @json($lightTheme);
            const legacyThemes = @json($legacyThemes);
            const cookieName = 'theme';
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
