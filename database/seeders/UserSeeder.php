<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $password = config('showroom.demo_password');
        if (! is_string($password) || strlen($password) < 8) {
            $this->command?->warn('Set DEMO_PASSWORD (at least 8 characters) to seed local demo users.');

            return;
        }

        $accounts = [
            ['admin', UserRole::Admin],
            ['sales1', UserRole::Sales],
            ['sales2', UserRole::Sales],
            ['customer1', UserRole::Customer],
            ['customer2', UserRole::Customer],
            ['customer3', UserRole::Customer],
        ];

        foreach ($accounts as [$name, $role]) {
            $email = $name.'@example.test';
            if (User::where('email', $email)->exists()) {
                continue; // Re-seeding must not reset an existing account's password or status.
            }
            $user = new User;
            $user->forceFill([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'is_active' => true,
            ])->save();
        }
    }
}
