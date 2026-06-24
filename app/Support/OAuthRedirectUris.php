<?php

declare(strict_types=1);

namespace App\Support;

class OAuthRedirectUris
{
    public static function yandexCallback(): string
    {
        return url('/auth/yandex/callback');
    }

    public static function yandexTokenPage(): string
    {
        return url('/auth/yandex/token-page');
    }

    public static function vkCallback(): string
    {
        return url('/auth/vk/callback');
    }

    /**
     * @return list<string>
     */
    public static function yandexConsoleUris(): array
    {
        return [
            self::yandexCallback(),
            self::yandexTokenPage(),
        ];
    }
}
