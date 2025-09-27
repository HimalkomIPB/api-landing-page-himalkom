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
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL')],
            [
                'name' => 'ristek',
                'password' => bcrypt(env('ADMIN_PASSWORD')),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => env('ADMIN_EDUCATION_EMAIL')],
            [
                'name' => 'ACE Admin',
                'password' => bcrypt(env('ADMIN_EDUCATION_PASSWORD')),
                'email_verified_at' => now(),
            ]
        );
    }
}
