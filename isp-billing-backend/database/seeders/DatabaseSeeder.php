<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ========================================
        // 🌱 DATABASE SEEDERS - Urutan Eksekusi
        // ========================================
        // PENTING: Urutan ini harus diikuti karena ada foreign key dependencies
        
        $this->call([
            // 1. Subscription Packages (harus pertama, tidak ada dependency)
            SubscriptionPackageSeeder::class,
            
            // 2. Landing Page Content (tidak ada dependency)
            LandingPageSeeder::class,
            
            // 3. Super Admin User (tidak ada dependency)
            UserSeeder::class,
            
            // ========================================
            // 📝 SEEDERS YANG DINONAKTIFKAN
            // ========================================
            // ISPSeeder::class, 
            // - Tidak ada data ISP default
            // - ISP akan dibuat saat registrasi
            
            // PaymentGatewaySeeder::class,
            // - Payment gateway dikonfigurasi manual oleh super admin
            // - Tidak perlu data default
        ]);
        
        // ========================================
        // 💡 CARA MENJALANKAN SEEDERS
        // ========================================
        // Semua seeders:
        // php artisan db:seed
        //
        // Seeder tertentu:
        // php artisan db:seed --class=LandingPageSeeder
        //
        // Reset database + seeders:
        // php artisan migrate:fresh --seed
        // ⚠️ HATI-HATI: Ini akan menghapus SEMUA data!
    }
}
