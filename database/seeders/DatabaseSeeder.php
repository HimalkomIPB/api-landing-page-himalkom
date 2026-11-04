<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
            'admin' => 'ristek',
            'admin2' => 'ristek 2 (restricted)',
            'admin_education' => 'ACE Admin',
        ] as $key => $name) {
            $email = config("admin.{$key}_email");
            $password = config("admin.{$key}_password");

            if (blank($email) || blank($password)) {
                continue;
            }

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => bcrypt($password),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
