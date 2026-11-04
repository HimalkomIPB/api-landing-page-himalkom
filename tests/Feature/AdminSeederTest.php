<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

it('skips admin accounts with incomplete credentials', function (?string $email, ?string $password) {
    config()->set('admin', []);

    foreach (['admin', 'admin2', 'admin_education'] as $key) {
        config()->set("admin.{$key}_email", $email);
        config()->set("admin.{$key}_password", $password);
    }

    DB::shouldReceive('connection')->never();

    (new DatabaseSeeder)->run();
})->with([
    [null, null],
    ['', ''],
    [' ', ' '],
    ['admin@example.com', ''],
    ['', 'test-password'],
]);
