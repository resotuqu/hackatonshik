<?php

namespace App\Support;

class ThemeResolver
{
    public static function fromCookie(?string $cookieTheme): string
    {
        $dark = config('theme.dark');
        $light = config('theme.light');
        /** @var array<string, string> $legacy */
        $legacy = config('theme.legacy', []);
        $allowed = array_merge([$dark, $light], array_keys($legacy));

        if ($cookieTheme !== null && in_array($cookieTheme, $allowed, true)) {
            return $legacy[$cookieTheme] ?? $cookieTheme;
        }

        return $dark;
    }

    /**
     * @return list<string>
     */
    public static function allowedThemes(): array
    {
        return array_values(array_unique(array_merge(
            [config('theme.dark'), config('theme.light')],
            array_keys(config('theme.legacy', [])),
        )));
    }
}
