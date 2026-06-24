<?php

declare(strict_types=1);

use App\Support\TrustedProxiesResolver;

test('trusted proxies resolver trusts all proxies in local environment', function () {
    $original = getenv('APP_ENV');

    putenv('APP_ENV=local');

    expect(TrustedProxiesResolver::at())->toBe('*');

    if ($original !== false) {
        putenv('APP_ENV='.$original);
    } else {
        putenv('APP_ENV');
    }
});

test('trusted proxies resolver parses comma separated production proxies', function () {
    $originalEnv = getenv('APP_ENV');
    $originalProxies = getenv('TRUSTED_PROXIES');

    putenv('APP_ENV=production');
    putenv('TRUSTED_PROXIES=10.0.0.1, 10.0.0.2');

    expect(TrustedProxiesResolver::at())->toBe(['10.0.0.1', '10.0.0.2']);

    if ($originalEnv !== false) {
        putenv('APP_ENV='.$originalEnv);
    } else {
        putenv('APP_ENV');
    }

    if ($originalProxies !== false) {
        putenv('TRUSTED_PROXIES='.$originalProxies);
    } else {
        putenv('TRUSTED_PROXIES');
    }
});
