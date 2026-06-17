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
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-site');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'credentialless');

        $csp = "default-src 'self'; ".
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'; ". // unsafe-eval often needed for Alpine/Livewire in some edge cases, but keep it if needed. Actually Mary/Livewire 4 might need it.
               "style-src 'self' 'unsafe-inline'; ".
               "img-src 'self' data: https:; ".
               "font-src 'self' data:; ".
               "connect-src 'self' ".(config('app.env') === 'local' ? 'ws: wss:' : 'wss:').'; '.
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
