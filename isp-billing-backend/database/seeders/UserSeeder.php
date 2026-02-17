<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ========================================
        // 👤 SUPER ADMIN - User Default
        // ========================================
        // PENTING: Ubah password setelah login pertama kali!
        
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@ispbilling.com',
            'password' => Hash::make('password'), // ⚠️ UBAH PASSWORD INI!
            'role' => 'super_admin',
            'isp_id' => null, // Super admin tidak terikat ISP
            'phone' => '081234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        
        // ========================================
        // 📝 LOGIN CREDENTIALS
        // ========================================
        // Email: superadmin@ispbilling.com
        // Password: password
        //
        // ⚠️ SEGERA UBAH PASSWORD SETELAH LOGIN PERTAMA KALI!
        
        // ========================================
        // 💡 TIPS KUSTOMISASI
        // ========================================
        // 1. Ubah email dan password di atas
        // 2. Bisa tambah user lain jika perlu
        // 3. Role yang tersedia:
        //    - super_admin: Akses penuh
        //    - isp_admin: Admin ISP
        //    - technician: Teknisi
        //    - customer: Pelanggan
    }
}
