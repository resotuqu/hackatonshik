<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), browsing-topics=()');
        // VK ID SDK opens a popup for auth — same-origin blocks it.
        // COOP is ignored on non-HTTPS origins (e.g. local .test), so skip it there.
        if ($request->isSecure()) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        }

        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'credentialless');

        $csp = "default-src 'self'; ".
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://yastatic.net; ".
               "style-src 'self' 'unsafe-inline' https://yastatic.net; ".
               "img-src 'self' data: https:; ".
               "font-src 'self' data: https://yastatic.net; ".
               "connect-src 'self' https://id.vk.com https://login.yandex.ru https://autofill.yandex.ru https://yastatic.net ".(config('app.env') === 'local' ? 'ws: wss:' : 'wss:').'; '.
               "frame-src 'self' https://id.vk.com https://oauth.vk.com https://yandex.ru https://passport.yandex.ru https://autofill.yandex.ru; ".
               "base-uri 'self'; ".
               "form-action 'self'; ".
               "object-src 'none'; ".
               "frame-ancestors 'none';";

        if (app()->isProduction()) {
            $csp .= ' upgrade-insecure-requests;';
        }

        $response->headers->set('Content-Security-Policy', $csp);

        // Add HSTS only in production (to avoid blocking local dev)
        if (app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
