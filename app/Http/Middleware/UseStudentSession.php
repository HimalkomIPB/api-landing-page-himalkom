<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Switches to a dedicated session cookie before the session starts, keeping student
 * identity isolated from the CMS (Filament) session.
 */
class UseStudentSession
{
    public function handle(Request $request, Closure $next): Response
    {
        config([
            'session.cookie' => config('student_auth.session_cookie'),
            'session.http_only' => true,
            'session.same_site' => 'lax',
        ]);

        app('session')->driver()->setName(config('student_auth.session_cookie'));

        return $next($request);
    }
}
