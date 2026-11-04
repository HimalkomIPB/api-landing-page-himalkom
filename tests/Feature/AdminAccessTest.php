<?php

use App\Models\User;
use Filament\Panel;

beforeEach(function () {
    config()->set('admin', [
        'admin_email' => 'test@gmail.com',
        'admin2_email' => 'restricted@gmail.com',
        'admin_education_email' => 'education@gmail.com',
    ]);
});

it('allows verified configured admins to access the panel', function (string $email) {
    $user = (new User)->forceFill([
        'email' => $email,
        'email_verified_at' => now(),
    ]);

    expect($user->canAccessPanel(Panel::make()))->toBeTrue();
})->with(['test@gmail.com', 'restricted@gmail.com', 'education@gmail.com']);

it('preserves access for verified himalkom accounts', function () {
    $user = (new User)->forceFill([
        'email' => 'staff@himalkom.com',
        'email_verified_at' => now(),
    ]);

    expect($user->canAccessPanel(Panel::make()))->toBeTrue();
});

it('rejects unverified admins and unconfigured external accounts', function (string $email, bool $verified) {
    $user = (new User)->forceFill([
        'email' => $email,
        'email_verified_at' => $verified ? now() : null,
    ]);

    expect($user->canAccessPanel(Panel::make()))->toBeFalse();
})->with([
    ['test@gmail.com', false],
    ['staff@himalkom.com', false],
    ['unknown@gmail.com', true],
    ['staff@himalkom.com.example.org', true],
]);

it('does not grant access through empty admin configuration', function () {
    config()->set('admin', [
        'admin_email' => '',
        'admin2_email' => null,
        'admin_education_email' => ' ',
    ]);

    $user = (new User)->forceFill([
        'email' => '',
        'email_verified_at' => now(),
    ]);

    expect($user->canAccessPanel(Panel::make()))->toBeFalse();
});
