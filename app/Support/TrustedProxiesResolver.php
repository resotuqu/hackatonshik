<?php

namespace App\Support;

class TrustedProxiesResolver
{
    /**
     * Resolve trusted proxy IPs for middleware bootstrap (before config is loaded).
     *
     * @return array<int, string>|string
     */
    public static function at(): array|string
    {
        $appEnv = env('APP_ENV', 'production');

        if (in_array($appEnv, ['local', 'testing'], true)) {
            return '*';
        }

        $trusted = env('TRUSTED_PROXIES');

        if ($trusted === '*') {
            return '*';
        }

        if (is_string($trusted) && $trusted !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $trusted))));
        }

        return [];
    }
}
