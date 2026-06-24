<?php

use App\Http\Middleware\EnsureContactChannelsVerified;
use App\Http\Middleware\EnsureJudge;
use App\Http\Middleware\EnsureOrganizer;
use App\Http\Middleware\EnsureParticipant;
use App\Http\Middleware\EnsureUserNotSuspended;
use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Support\TrustedProxiesResolver;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\ValidateSignature;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: [
            'theme',
        ]);

        $middleware->validateCsrfTokens();

        // Signed mail links must validate after HTTPS redirects / proxies; relative signatures ignore scheme & host.
        $middleware->alias([
            'signed' => ValidateSignature::class.':relative',
            'organizer' => EnsureOrganizer::class,
            'judge' => EnsureJudge::class,
            'participant' => EnsureParticipant::class,
        ]);

        $middleware->trustProxies(at: TrustedProxiesResolver::at());

        $middleware->web(append: [
            SecurityHeaders::class,
            SetLocale::class,
            EnsureUserNotSuspended::class,
            EnsureContactChannelsVerified::class,
        ]);

        $middleware->web(prepend: [
            ForceHttps::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
