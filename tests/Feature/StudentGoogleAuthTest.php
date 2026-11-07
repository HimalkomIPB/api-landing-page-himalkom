<?php

// Deterministic checks with MOCKED Google responses (Http::fake). These do not prove a real Google login.

use Illuminate\Support\Facades\Http;

const FRONTEND = 'https://staging.himalkom-ipb.com';

beforeEach(function () {
    config()->set('student_auth.google_client_id', 'test-client');
    config()->set('student_auth.google_client_secret', 'test-secret');
    config()->set('student_auth.google_redirect_uri', 'https://staging-api.test/auth/google/callback');
    config()->set('student_auth.frontend_url', FRONTEND);
    config()->set('student_auth.portal_admin_emails', 'portal.admin@apps.ipb.ac.id');
    config()->set('admin', [
        'admin_email' => 'Cms.Admin@apps.ipb.ac.id',
        'admin2_email' => 'other@gmail.com',
        'admin_education_email' => null,
    ]);
});

function fakeGoogle(array $claims): void
{
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response(['access_token' => 'at', 'id_token' => 'x']),
        'openidconnect.googleapis.com/v1/userinfo' => Http::response($claims),
    ]);
}

function startLogin($test): string
{
    $response = $test->get('/auth/google/redirect');
    $response->assertRedirect();
    $location = $response->headers->get('Location');
    expect($location)->toStartWith('https://accounts.google.com/o/oauth2/v2/auth?');
    parse_str(parse_url($location, PHP_URL_QUERY), $query);
    expect($query['hd'])->toBe('apps.ipb.ac.id');

    return $query['state'];
}

function claims(array $overrides = []): array
{
    return array_merge([
        'sub' => '1234567890',
        'email' => 'student@apps.ipb.ac.id',
        'email_verified' => true,
        'hd' => 'apps.ipb.ac.id',
        'name' => 'Student Test',
    ], $overrides);
}

it('signs in a verified apps.ipb.ac.id account and redirects to the dashboard', function () {
    fakeGoogle(claims());
    $state = startLogin($this);

    $this->get('/auth/google/callback?code=abc&state='.$state)->assertRedirect(FRONTEND.'/dashboard');

    $this->withHeader('Origin', FRONTEND)->getJson('/student/me')
        ->assertOk()
        ->assertJsonPath('student.email', 'student@apps.ipb.ac.id')
        ->assertJsonPath('is_admin', false)
        ->assertHeader('Cache-Control', 'no-store, private');
});

it('rejects other and lookalike domains', function (array $override) {
    fakeGoogle(claims($override));
    $state = startLogin($this);

    $this->get('/auth/google/callback?code=abc&state='.$state)->assertRedirect(FRONTEND.'/login?error=domain');
    $this->getJson('/student/me')->assertUnauthorized();
})->with([
    'gmail' => [['email' => 'student@gmail.com', 'hd' => null]],
    'ipb.ac.id' => [['email' => 'student@ipb.ac.id', 'hd' => 'ipb.ac.id']],
    'suffix lookalike' => [['email' => 'student@evilapps.ipb.ac.id', 'hd' => 'evilapps.ipb.ac.id']],
    'subdomain' => [['email' => 'student@x.apps.ipb.ac.id', 'hd' => 'x.apps.ipb.ac.id']],
    'prefix lookalike' => [['email' => 'student@apps.ipb.ac.id.evil.com', 'hd' => 'apps.ipb.ac.id']],
    'hd missing' => [['hd' => null]],
    'hd mismatch' => [['hd' => 'gmail.com']],
    'double at' => [['email' => 'a@apps.ipb.ac.id@apps.ipb.ac.id']],
]);

it('rejects unverified emails', function ($verified) {
    fakeGoogle(claims(['email_verified' => $verified]));
    $state = startLogin($this);

    $this->get('/auth/google/callback?code=abc&state='.$state)->assertRedirect(FRONTEND.'/login?error=domain');
    $this->getJson('/student/me')->assertUnauthorized();
})->with([false, 'true', null]);

it('rejects missing, invalid and replayed OAuth state', function () {
    fakeGoogle(claims());

    $this->get('/auth/google/callback?code=abc')->assertRedirect(FRONTEND.'/login?error=state');

    $state = startLogin($this);
    $this->get('/auth/google/callback?code=abc&state=wrong')->assertRedirect(FRONTEND.'/login?error=state');
    // State is single use: the failed attempt consumed it.
    $this->get('/auth/google/callback?code=abc&state='.$state)->assertRedirect(FRONTEND.'/login?error=state');

    $this->getJson('/student/me')->assertUnauthorized();
    Http::assertNothingSent();
});

it('reports cancellation, provider errors and missing configuration safely', function () {
    $state = startLogin($this);
    $this->get('/auth/google/callback?error=access_denied&state='.$state)->assertRedirect(FRONTEND.'/login?error=cancelled');

    Http::fake(['oauth2.googleapis.com/token' => Http::response(['error' => 'invalid_grant'], 400)]);
    $state = startLogin($this);
    $this->get('/auth/google/callback?code=abc&state='.$state)->assertRedirect(FRONTEND.'/login?error=provider');

    config()->set('student_auth.google_client_secret', null);
    $this->get('/auth/google/redirect')->assertRedirect(FRONTEND.'/login?error=unavailable');
});

it('grants admin only to exact emails in the CMS or portal admin lists', function (string $email, bool $admin) {
    fakeGoogle(claims(['email' => $email]));
    $state = startLogin($this);
    $this->get('/auth/google/callback?code=abc&state='.$state)->assertRedirect(FRONTEND.'/dashboard');

    $this->withHeader('Origin', FRONTEND)->getJson('/student/me')->assertJsonPath('is_admin', $admin);
    $this->withHeader('Origin', FRONTEND)->getJson('/student/admin')->assertStatus($admin ? 200 : 403);
})->with([
    'cms admin (case-insensitive)' => ['cms.admin@apps.ipb.ac.id', true],
    'portal admin' => ['portal.admin@apps.ipb.ac.id', true],
    'student' => ['student@apps.ipb.ac.id', false],
    'near miss' => ['cms.admin2@apps.ipb.ac.id', false],
]);

it('denies protected endpoints to guests and foreign origins', function () {
    $this->getJson('/student/me')->assertUnauthorized();
    $this->getJson('/student/admin')->assertUnauthorized();
    $this->withHeader('Origin', 'https://evil.example')->getJson('/student/me')->assertForbidden();
    $this->postJson('/student/logout')->assertForbidden();
});

it('does not reuse the CMS session or grant CMS access', function () {
    fakeGoogle(claims(['email' => 'cms.admin@apps.ipb.ac.id']));
    $state = startLogin($this);
    $this->get('/auth/google/callback?code=abc&state='.$state);

    expect(auth()->guard('web')->check())->toBeFalse();
    $this->get('/admin')->assertRedirect();
});

it('regenerates the session id on login', function () {
    fakeGoogle(claims());
    $state = startLogin($this);
    $before = session()->getId();

    $this->get('/auth/google/callback?code=abc&state='.$state);

    expect(session()->getId())->not->toBe($before);
});

it('logs out only with a valid CSRF token from the allowed origin and invalidates the session', function () {
    fakeGoogle(claims());
    $state = startLogin($this);
    $this->get('/auth/google/callback?code=abc&state='.$state);

    $token = $this->withHeader('Origin', FRONTEND)->getJson('/student/me')->json('csrf_token');

    $this->withHeaders(['Origin' => FRONTEND])->postJson('/student/logout')->assertStatus(419);
    $this->withHeaders(['Origin' => FRONTEND, 'X-CSRF-TOKEN' => 'nope'])->postJson('/student/logout')->assertStatus(419);
    $this->withHeaders(['Origin' => 'https://evil.example', 'X-CSRF-TOKEN' => $token])->postJson('/student/logout')->assertForbidden();
    $this->withHeader('Origin', FRONTEND)->getJson('/student/me')->assertOk();

    $this->withHeaders(['Origin' => FRONTEND, 'X-CSRF-TOKEN' => $token])->postJson('/student/logout')->assertOk();
    $this->withHeader('Origin', FRONTEND)->getJson('/student/me')->assertUnauthorized();
});

it('uses a dedicated student session cookie', function () {
    $response = $this->get('/auth/google/redirect');
    $names = array_map(fn ($c) => $c->getName(), $response->headers->getCookies());

    expect($names)->toContain('himalkom_student_session');
});
