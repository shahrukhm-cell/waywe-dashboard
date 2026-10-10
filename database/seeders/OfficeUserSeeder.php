<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OfficeUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'office@example.com';
        $password = 'password';

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Office User',
                'email_verified_at' => now(),
                'password' => Hash::make($password),
            ]
        );

        $this->command?->info("Office login created: {$email}");
        $this->command?->info("Temporary password: {$password}");
    }
}
