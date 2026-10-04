<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('starter.admin.email');
        $password = config('starter.admin.password');
        if (! $email || ! $password) {
            throw new RuntimeException('Set STARTER_ADMIN_EMAIL and STARTER_ADMIN_PASSWORD before seeding.');
        }
        $admin = User::firstOrCreate(['email' => $email], ['name' => 'Super Admin', 'username' => 'admin', 'password' => $password]);
        $admin->forceFill(['email_verified_at' => now()])->save();
        $admin->assignRole('super-admin');
    }
}
