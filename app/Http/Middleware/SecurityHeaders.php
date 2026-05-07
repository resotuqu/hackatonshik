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

        // Don't add headers if it's a binary file download, etc.
        if (! method_exists($response, 'headers')) {
            return $response;
        }

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        $csp = "default-src 'self'; ".
               "script-src 'self' 'unsafe-inline'; ".
               "style-src 'self' 'unsafe-inline'; ".
               "img-src 'self' data: https:; ".
               "font-src 'self' data:; ".
               "connect-src 'self'; ".
               "base-uri 'self'; ".
               "form-action 'self'; ".
               "object-src 'none'; ".
               "frame-ancestors 'none';";
        $response->headers->set('Content-Security-Policy', $csp);

        // Add HSTS only in production (to avoid blocking local dev)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
