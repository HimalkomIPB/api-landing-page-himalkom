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

        $allowedDomain = env('ALLOWED_ORIGIN');
        $allowedDomain = parse_url($allowedDomain, PHP_URL_HOST);

        $origin = $request->headers->get('Origin');
        $referer = $request->headers->get('Referer');

        if (!$origin || !$referer) return response()->json(['errors' => 'Forbidden'], 403);

        if (
            ($origin && parse_url($origin, PHP_URL_HOST) !== $allowedDomain) &&
            ($referer && parse_url($referer, PHP_URL_HOST) !== $allowedDomain)
        ) {
            return response()->json(['errors' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
