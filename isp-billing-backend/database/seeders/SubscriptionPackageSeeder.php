<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPackageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('subscription_packages')->insert([
            [
                'name' => 'Starter',
                'description' => 'Paket starter untuk ISP kecil dengan maksimal 100 pelanggan',
                'monthly_price' => 500000,
                'quarterly_price' => 1400000,
                'semi_annual_price' => 2700000,
                'annual_price' => 5000000,
                'max_customers' => 100,
                'max_invoices' => 1200,
                'email_support' => 1,
                'whatsapp_support' => 0,
                'custom_branding' => 0,
                'api_access' => 0,
                'multi_user_access' => 0,
                'features' => json_encode([
                    'Manajemen Pelanggan (100)',
                    'Invoice & Billing',
                    'Email Support',
                    'Dashboard Monitoring',
                    'Laporan Dasar'
                ]),
                'is_active' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional',
                'description' => 'Paket professional untuk ISP menengah dengan maksimal 500 pelanggan',
                'monthly_price' => 1500000,
                'quarterly_price' => 4200000,
                'semi_annual_price' => 8100000,
                'annual_price' => 15000000,
                'max_customers' => 500,
                'max_invoices' => 6000,
                'email_support' => 1,
                'whatsapp_support' => 1,
                'custom_branding' => 1,
                'api_access' => 0,
                'multi_user_access' => 1,
                'features' => json_encode([
                    'Manajemen Pelanggan (500)',
                    'Invoice & Billing',
                    'Email + WhatsApp Support',
                    'Custom Branding',
                    'Multi User Access',
                    'Dashboard Advanced',
                    'Laporan Lengkap',
                    'Payment Gateway Integration'
                ]),
                'is_active' => 1,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Paket enterprise untuk ISP besar dengan unlimited pelanggan',
                'monthly_price' => 3500000,
                'quarterly_price' => 9800000,
                'semi_annual_price' => 18900000,
                'annual_price' => 35000000,
                'max_customers' => 0,
                'max_invoices' => 0,
                'email_support' => 1,
                'whatsapp_support' => 1,
                'custom_branding' => 1,
                'api_access' => 1,
                'multi_user_access' => 1,
                'features' => json_encode([
                    'Unlimited Pelanggan',
                    'Unlimited Invoice',
                    'Priority Support (Email, WhatsApp, Phone)',
                    'Custom Branding',
                    'Multi User Access',
                    'API Access',
                    'White Label',
                    'Dashboard Advanced',
                    'Laporan Custom',
                    'Payment Gateway Integration',
                    'Dedicated Account Manager'
                ]),
                'is_active' => 1,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
