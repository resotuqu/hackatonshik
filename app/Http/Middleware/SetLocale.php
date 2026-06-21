<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['ru', 'en'];

    private const COOKIE = 'locale';

    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolve($request));

        return $next($request);
    }

    private function resolve(Request $request): string
    {
        // 1. Authenticated user's saved preference
        if (Auth::check()) {
            $userLocale = Auth::user()->locale;
            if (is_string($userLocale) && in_array($userLocale, self::SUPPORTED, true)) {
                return $userLocale;
            }
        }

        // 2. Cookie set by the switcher
        $cookie = $request->cookie(self::COOKIE);
        if (is_string($cookie) && in_array($cookie, self::SUPPORTED, true)) {
            return $cookie;
        }

        // 3. Browser Accept-Language header
        $preferred = $request->getPreferredLanguage(self::SUPPORTED);
        if ($preferred !== null && in_array($preferred, self::SUPPORTED, true)) {
            return $preferred;
        }

        return config('app.locale', 'ru');
    }
}
