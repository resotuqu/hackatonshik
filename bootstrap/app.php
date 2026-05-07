<?php

use App\Http\Middleware\EnsureContactChannelsVerified;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ValidateSignature;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Signed mail links must validate after HTTPS redirects / proxies; relative signatures ignore scheme & host.
        $middleware->alias([
            'signed' => ValidateSignature::class.':relative',
        ]);

        $appEnv = (string) env('APP_ENV', 'production');
        $trusted = env('TRUSTED_PROXIES');
        $at = match (true) {
            in_array($appEnv, ['local', 'testing'], true) => '*',
            $trusted === '*' => '*',
            is_string($trusted) && $trusted !== '' => array_values(array_filter(array_map('trim', explode(',', $trusted)))),
            default => [],
        };
        $middleware->trustProxies(at: $at);

        $middleware->web(append: [
            SecurityHeaders::class,
            EnsureContactChannelsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
