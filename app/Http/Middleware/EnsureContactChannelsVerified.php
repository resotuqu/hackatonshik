<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureContactChannelsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($user->phone_verified_at === null) {
            return redirect()->route('phone.verify.notice');
        }

        return $next($request);
    }

    private function shouldBypass(Request $request): bool
    {
        if ($request->is(
            'livewire*/update',
            'livewire*/upload-file',
            'livewire*/preview-file',
            'livewire*/preview-file/*',
            'livewire*/js/*',
            'livewire*/css/*',
            '_boost/*',
            'storage/*',
            'up',
        )) {
            return true;
        }

        if ($request->is('auth/*')) {
            return true;
        }

        return $request->routeIs(
            'verification.notice',
            'verification.verify',
            'verification.send',
            'phone.verify.notice',
            'phone.verify.phone',
            'phone.verify.send',
            'phone.verify',
            'login',
            'login.store',
            'logout',
            'register',
            'register.store',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
            'two-factor.login',
            'two-factor.login.store',
            'password.confirm',
            'password.confirm.store',
            'password.confirmation',
            'two-factor.*',
        );
    }
}
