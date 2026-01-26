<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@ispbilling.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'isp_id' => null,
            'phone' => '081234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
