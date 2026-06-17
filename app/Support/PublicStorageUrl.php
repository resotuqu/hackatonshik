<?php

declare(strict_types=1);

namespace App\Support;

final class PublicStorageUrl
{
    /**
     * @var list<string>
     */
    private const ALLOWED_EXTERNAL_HOSTS = [
        'hackatonshik.ru',
        'www.hackatonshik.ru',
    ];

    public static function for(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $host = parse_url($path, PHP_URL_HOST);

            if (! is_string($host) || ! self::isAllowedExternalHost($host)) {
                return null;
            }

            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    private static function isAllowedExternalHost(string $host): bool
    {
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (is_string($appHost) && strcasecmp($host, $appHost) === 0) {
            return true;
        }

        foreach (self::ALLOWED_EXTERNAL_HOSTS as $allowedHost) {
            if (strcasecmp($host, $allowedHost) === 0) {
                return true;
            }
        }

        return false;
    }
}
