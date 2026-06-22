<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Redirect insecure requests to HTTPS when the application is configured for it.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.force_https') || $request->isSecure()) {
            return $next($request);
        }

        return redirect()->secure($request->getRequestUri(), 301);
    }
}
