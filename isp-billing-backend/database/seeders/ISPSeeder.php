<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ISP;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ISPSeeder extends Seeder
{
    public function run(): void
    {
        // Create ISP 1: NetSpeed Indonesia - Enterprise Package
        $isp1 = ISP::create([
            'company_name' => 'NetSpeed Indonesia',
            'business_license' => 'BIZ-001-2026',
            'address' => 'Jl. Sudirman No. 123',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12190',
            'phone' => '021-5551234',
            'email' => 'contact@netspeed.com',
            'website' => 'https://netspeed.com',
            'subscription_package_id' => 3, // Enterprise
            'subscription_status' => 'active',
            'subscription_start_date' => now()->subMonths(6),
            'subscription_end_date' => now()->addMonths(6),
            'billing_cycle' => 'monthly',
            'approval_status' => 'approved',
            'approved_at' => now()->subMonths(6),
            'approved_by' => 1,
            'is_active' => true,
        ]);

        // Create admin user for NetSpeed
        User::create([
            'name' => 'Admin NetSpeed',
            'email' => 'admin@netspeed.com',
            'password' => Hash::make('password'),
            'role' => 'isp_admin',
            'isp_id' => $isp1->id,
            'phone' => '081234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample customers for NetSpeed
        for ($i = 1; $i <= 450; $i++) {
            User::create([
                'name' => "Customer NetSpeed $i",
                'email' => "customer{$i}@netspeed.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'isp_id' => $isp1->id,
                'phone' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create ISP 2: FastNet ISP - Professional Package
        $isp2 = ISP::create([
            'company_name' => 'FastNet ISP',
            'business_license' => 'BIZ-002-2026',
            'address' => 'Jl. Gatot Subroto No. 456',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40123',
            'phone' => '022-7771234',
            'email' => 'info@fastnet.com',
            'website' => 'https://fastnet.com',
            'subscription_package_id' => 2, // Professional
            'subscription_status' => 'active',
            'subscription_start_date' => now()->subMonths(3),
            'subscription_end_date' => now()->addMonths(9),
            'billing_cycle' => 'monthly',
            'approval_status' => 'approved',
            'approved_at' => now()->subMonths(3),
            'approved_by' => 1,
            'is_active' => true,
        ]);

        // Create admin user for FastNet
        User::create([
            'name' => 'Admin FastNet',
            'email' => 'admin@fastnet.com',
            'password' => Hash::make('password'),
            'role' => 'isp_admin',
            'isp_id' => $isp2->id,
            'phone' => '081234567891',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample customers for FastNet
        for ($i = 1; $i <= 320; $i++) {
            User::create([
                'name' => "Customer FastNet $i",
                'email' => "customer{$i}@fastnet.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'isp_id' => $isp2->id,
                'phone' => '0822345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create ISP 3: CloudConnect - Starter Package (Trial)
        $isp3 = ISP::create([
            'company_name' => 'CloudConnect',
            'business_license' => 'BIZ-003-2026',
            'address' => 'Jl. Ahmad Yani No. 789',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'postal_code' => '60234',
            'phone' => '031-8881234',
            'email' => 'admin@cloudconnect.com',
            'website' => 'https://cloudconnect.com',
            'subscription_package_id' => 1, // Starter
            'subscription_status' => 'trial',
            'subscription_start_date' => now()->subDays(15),
            'subscription_end_date' => now()->addDays(15),
            'billing_cycle' => 'monthly',
            'approval_status' => 'approved',
            'approved_at' => now()->subDays(15),
            'approved_by' => 1,
            'is_active' => true,
        ]);

        // Create admin user for CloudConnect
        User::create([
            'name' => 'Admin CloudConnect',
            'email' => 'admin@cloudconnect.com',
            'password' => Hash::make('password'),
            'role' => 'isp_admin',
            'isp_id' => $isp3->id,
            'phone' => '081234567892',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create sample customers for CloudConnect
        for ($i = 1; $i <= 150; $i++) {
            User::create([
                'name' => "Customer CloudConnect $i",
                'email' => "customer{$i}@cloudconnect.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'isp_id' => $isp3->id,
                'phone' => '0832345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create 2 pending ISPs for approval
        ISP::create([
            'company_name' => 'MegaNet Solutions',
            'business_license' => 'BIZ-004-2026',
            'address' => 'Jl. Diponegoro No. 321',
            'city' => 'Semarang',
            'province' => 'Jawa Tengah',
            'postal_code' => '50132',
            'phone' => '024-7771234',
            'email' => 'info@meganet.com',
            'website' => 'https://meganet.com',
            'subscription_package_id' => 2,
            'subscription_status' => 'trial',
            'approval_status' => 'pending',
            'is_active' => false,
        ]);

        ISP::create([
            'company_name' => 'SpeedyConnect ISP',
            'business_license' => 'BIZ-005-2026',
            'address' => 'Jl. Malioboro No. 111',
            'city' => 'Yogyakarta',
            'province' => 'DI Yogyakarta',
            'postal_code' => '55213',
            'phone' => '0274-5551234',
            'email' => 'contact@speedyconnect.com',
            'website' => 'https://speedyconnect.com',
            'subscription_package_id' => 1,
            'subscription_status' => 'trial',
            'approval_status' => 'pending',
            'is_active' => false,
        ]);
    }
}
