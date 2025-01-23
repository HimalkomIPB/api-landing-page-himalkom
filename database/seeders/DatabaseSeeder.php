<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'ristek',
            'email' => env("ADMIN_EMAIL"),
            'password' => bcrypt(env("ADMIN_PASSWORD")),
            'email_verified_at' => now(),
        ]);
    }
}
