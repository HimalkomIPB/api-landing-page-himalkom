<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rejects cross-origin requests to student JSON endpoints unless they come from the
 * configured frontend origin. Browsers always send Origin on credentialed CORS requests.
 */
class RequireStudentOrigin
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        $allowed = config('student_auth.frontend_url');

        if ($origin !== null && $origin !== $allowed) {
            return response()->json(['message' => 'Forbidden origin.'], 403);
        }

        if ($request->isMethod('POST') && $origin === null) {
            return response()->json(['message' => 'Forbidden origin.'], 403);
        }

        return $next($request);
    }
}
