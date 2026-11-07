<?php

namespace App\Http\Controllers\StudentAuth;

use App\Http\Controllers\Controller;
use App\Support\StudentAdmins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GoogleAuthController extends Controller
{
    private const AUTHORIZE_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const USERINFO_URL = 'https://openidconnect.googleapis.com/v1/userinfo';

    public function redirect(Request $request): RedirectResponse
    {
        if (! $this->configured()) {
            return $this->toLogin('unavailable');
        }

        $state = Str::random(64);
        $request->session()->put('student_oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('student_auth.google_client_id'),
            'redirect_uri' => config('student_auth.google_redirect_uri'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            // UI hint only; the domain is enforced again after the token exchange.
            'hd' => config('student_auth.allowed_domain'),
            'prompt' => 'select_account',
        ]);

        return redirect()->away(self::AUTHORIZE_URL.'?'.$query);
    }

    public function callback(Request $request): RedirectResponse
    {
        $expected = $request->session()->pull('student_oauth_state');
        $state = $request->query('state');

        if (! is_string($expected) || ! is_string($state) || ! hash_equals($expected, $state)) {
            return $this->toLogin('state');
        }

        if ($request->query('error') !== null) {
            return $this->toLogin($request->query('error') === 'access_denied' ? 'cancelled' : 'provider');
        }

        $code = $request->query('code');
        if (! is_string($code) || $code === '' || ! $this->configured()) {
            return $this->toLogin($this->configured() ? 'provider' : 'unavailable');
        }

        try {
            $token = Http::asForm()->timeout(10)->post(self::TOKEN_URL, [
                'code' => $code,
                'client_id' => config('student_auth.google_client_id'),
                'client_secret' => config('student_auth.google_client_secret'),
                'redirect_uri' => config('student_auth.google_redirect_uri'),
                'grant_type' => 'authorization_code',
            ]);

            $accessToken = $token->successful() ? $token->json('access_token') : null;
            if (! is_string($accessToken) || $accessToken === '') {
                return $this->toLogin('provider');
            }

            $profile = Http::withToken($accessToken)->timeout(10)->get(self::USERINFO_URL);
            if (! $profile->successful()) {
                return $this->toLogin('provider');
            }
            $claims = $profile->json();
        } catch (Throwable $e) {
            Log::warning('Student Google sign-in failed', ['exception' => $e::class]);

            return $this->toLogin('provider');
        }

        if (! is_array($claims) || ! $this->claimsAllowed($claims)) {
            return $this->toLogin('domain');
        }

        // Fresh session id on login (fixation protection); discard anything pre-login.
        $request->session()->invalidate();
        $request->session()->put('student', [
            'sub' => (string) $claims['sub'],
            'email' => strtolower($claims['email']),
            'name' => is_string($claims['name'] ?? null) ? Str::limit($claims['name'], 120, '') : null,
        ]);

        return redirect()->away(config('student_auth.frontend_url').'/dashboard');
    }

    public function me(Request $request): JsonResponse
    {
        $student = $request->session()->get('student');

        if (! is_array($student)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'student' => [
                'email' => $student['email'],
                'name' => $student['name'],
            ],
            'is_admin' => StudentAdmins::isAdmin($student['email']),
            'csrf_token' => $request->session()->token(),
        ])->header('Cache-Control', 'no-store');
    }

    public function admin(Request $request): JsonResponse
    {
        $student = $request->session()->get('student');

        if (! is_array($student)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! StudentAdmins::isAdmin($student['email'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json(['is_admin' => true])->header('Cache-Control', 'no-store');
    }

    public function logout(Request $request): JsonResponse
    {
        // Explicit check (the framework CSRF middleware is bypassed during unit tests).
        $token = $request->header('X-CSRF-TOKEN');
        if (! is_string($token) || ! hash_equals($request->session()->token(), $token)) {
            return response()->json(['message' => 'Invalid CSRF token.'], 419);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Signed out.']);
    }

    /**
     * @param  array<string, mixed>  $claims
     */
    public static function claimsAllowed(array $claims): bool
    {
        $domain = config('student_auth.allowed_domain');
        $email = $claims['email'] ?? null;

        if (! is_string($email) || ! is_string($claims['sub'] ?? null) || $claims['sub'] === '') {
            return false;
        }

        if (($claims['email_verified'] ?? null) !== true) {
            return false;
        }

        $email = strtolower(trim($email));
        $at = strrpos($email, '@');
        if ($at === false || substr_count($email, '@') !== 1 || substr($email, $at + 1) !== $domain) {
            return false;
        }

        // Google sets hd only for Workspace-managed accounts; require it to match exactly.
        return ($claims['hd'] ?? null) === $domain;
    }

    private function configured(): bool
    {
        return filled(config('student_auth.google_client_id'))
            && filled(config('student_auth.google_client_secret'))
            && filled(config('student_auth.frontend_url'))
            && config('student_auth.frontend_url') !== '*';
    }

    private function toLogin(string $error): RedirectResponse
    {
        $frontend = config('student_auth.frontend_url');

        if (! filled($frontend) || $frontend === '*') {
            abort(503, 'Student sign-in is not configured.');
        }

        return redirect()->away($frontend.'/login?error='.$error);
    }
}
