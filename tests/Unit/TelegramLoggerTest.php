<?php

declare(strict_types=1);

use App\Logging\TelegramLogger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

test('telegram logger redacts sensitive context keys', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => true]),
    ]);

    config([
        'logging.channels.telegram' => [
            'driver' => 'custom',
            'via' => TelegramLogger::class,
            'level' => 'critical',
            'token' => 'test-token',
            'chat_id' => '12345',
        ],
    ]);

    Log::channel('telegram')->critical('Probe message', [
        'password' => 'secret-password',
        'user' => 'safe-value',
    ]);

    Http::assertSent(function ($request): bool {
        $body = $request->body();

        return str_contains($body, '[REDACTED]')
            && ! str_contains($body, 'secret-password')
            && str_contains($body, 'safe-value');
    });
});

test('telegram logger skips network call when credentials are missing', function () {
    Http::fake();

    config([
        'logging.channels.telegram' => [
            'driver' => 'custom',
            'via' => TelegramLogger::class,
            'level' => 'critical',
            'token' => '',
            'chat_id' => '',
        ],
    ]);

    Log::channel('telegram')->critical('No credentials probe');

    Http::assertNothingSent();
});
