@php
    use App\Support\ThemeResolver;

    $serverTheme = ThemeResolver::fromCookie(request()->cookie('theme'));
    $darkTheme = config('theme.dark');
    $lightTheme = config('theme.light');
    $legacyThemes = config('theme.legacy');
@endphp
<!DOCTYPE html>
<html lang="ru" data-theme="{{ $serverTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Страница не найдена</title>
    <script>
        (function () {
            const darkTheme = @json($darkTheme);
            const lightTheme = @json($lightTheme);
            const legacyThemes = @json($legacyThemes);
            const cookieMatch = document.cookie.match(/(?:^|; )theme=([^;]*)/);
            const saved = cookieMatch ? decodeURIComponent(cookieMatch[1]) : null;

            if (saved === darkTheme || saved === lightTheme) {
                document.documentElement.setAttribute('data-theme', saved);
            } else if (saved && legacyThemes[saved]) {
                document.documentElement.setAttribute('data-theme', legacyThemes[saved]);
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base-100 text-base-content">
    <div class="flex min-h-screen flex-col items-center justify-center gap-6 px-4 text-center">
        <p class="font-display text-8xl font-bold text-primary/20 sm:text-9xl" aria-hidden="true">404</p>

        <div class="space-y-2">
            <h1 class="font-display text-2xl font-bold sm:text-3xl">Страница не найдена</h1>
            <p class="t-body text-base-content/70">Возможно, ссылка устарела или страница была удалена.</p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('hackatons.index') }}" class="ui-cta-primary">
                Хакатоны
            </a>
            <a href="/" class="ui-cta-outline">
                На главную
            </a>
        </div>
    </div>
</body>
</html>
