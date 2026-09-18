<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictToFrontendDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local', 'development')) {
            return $next($request);
        }

        $allowedOrigin = config('app.allowed_origin');
        
        if ($allowedOrigin === '*') {
            return $next($request);
        }

        $allowedDomain = parse_url($allowedOrigin, PHP_URL_HOST) ?: $allowedOrigin;

        $origin = $request->headers->get('Origin');
        $referer = $request->headers->get('Referer');

        if (! $origin && ! $referer) {
            return response()->json(['errors' => 'Forbidden'], 403);
        }

        $originDomain = $origin ? parse_url($origin, PHP_URL_HOST) : null;
        $refererDomain = $referer ? parse_url($referer, PHP_URL_HOST) : null;

        if ($originDomain && $originDomain !== $allowedDomain) {
            return response()->json(['errors' => 'Forbidden'], 403);
        }

        if ($refererDomain && $refererDomain !== $allowedDomain) {
            return response()->json(['errors' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
