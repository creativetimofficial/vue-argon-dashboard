<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubscriptionPackageSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('subscription_packages')->truncate();
        Schema::enableForeignKeyConstraints();
        
        // ========================================
        // 💰 PAKET SUBSCRIPTION - Harga & Fitur
        // ========================================
        
        DB::table('subscription_packages')->insert([
            
            // ========================================
            // 🎁 PAKET TRIAL - Gratis 7 Hari
            // ========================================
            [
                'name' => 'Trial',
                'slug' => 'trial',
                'description' => 'Paket trial gratis untuk mencoba fitur dasar selama 7 hari',
                
                'price' => 0,
                'active_days' => 7,
                
                // Limit
                'max_customers' => 10,
                'max_invoices' => 0,
                'max_users' => 1,
                'max_locations' => 1,
                
                // Support
                'email_support' => 1,
                'whatsapp_support' => 0,
                'custom_branding' => 0,
                'multi_user_access' => 0,
                
                // Feature Flags - Fitur Dasar Saja
                'feature_free_subdomain' => 1,          // ✅ Free Subdomain
                'feature_custom_domain' => 0,           // ❌ Custom Domain
                'feature_maps_interaktif' => 0,         // ❌ Maps Interaktif
                'feature_dynamic_forwarding' => 0,      // ❌ Dynamic Forwarding ONT
                'feature_realtime_monitoring' => 0,     // ❌ Monitoring Realtime
                'feature_radius' => 0,                  // ❌ Radius
                'feature_acs' => 0,                     // ❌ ACS
                'feature_android_app' => 0,             // ❌ Android App
                'feature_payment_gateways' => 0,        // ❌ Payment Gateway
                'feature_api_access' => 0,              // ❌ API Access
                'feature_custom_landing_page' => 0,     // ❌ Custom Landing Page
                'feature_customer_portal' => 0,         // ❌ Customer Portal
                'feature_whatsapp_gateway' => 0,        // ❌ WhatsApp Gateway
                
                // Flags
                'requires_manual_approval' => 1,
                'is_popular' => 0,
                'is_active' => 1,
                'sort_order' => 0,
                
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ========================================
            // 🌱 PAKET STARTER - Untuk ISP Kecil
            // ========================================
            [
                'name' => 'Starter (30 Hari)',
                'slug' => 'starter-30',
                'description' => 'Paket starter untuk ISP kecil dengan masa aktif 30 hari',
                
                'price' => 50000,
                'active_days' => 30,
                
                // Limit
                'max_customers' => 100,
                'max_invoices' => 1200,
                'max_users' => 2,
                'max_locations' => 1,
                
                // Support
                'email_support' => 1,
                'whatsapp_support' => 0,
                'custom_branding' => 0,
                'multi_user_access' => 0,
                
                // Feature Flags - Fitur Menengah
                'feature_free_subdomain' => 1,          // ✅ Free Subdomain
                'feature_custom_domain' => 0,           // ❌ Custom Domain
                'feature_maps_interaktif' => 1,         // ✅ Maps Interaktif
                'feature_dynamic_forwarding' => 0,      // ❌ Dynamic Forwarding ONT
                'feature_realtime_monitoring' => 1,     // ✅ Monitoring Realtime
                'feature_radius' => 0,                  // ❌ Radius
                'feature_acs' => 0,                     // ❌ ACS
                'feature_android_app' => 1,             // ✅ Android App
                'feature_payment_gateways' => 1,        // ✅ Payment Gateway (1 gateway)
                'feature_api_access' => 0,              // ❌ API Access
                'feature_custom_landing_page' => 1,     // ✅ Custom Landing Page (basic)
                'feature_customer_portal' => 1,         // ✅ Customer Portal
                'feature_whatsapp_gateway' => 1,        // ✅ WhatsApp Gateway (50 msg/month)
                
                // Flags
                'requires_manual_approval' => 0,
                'is_popular' => 0,
                'is_active' => 1,
                'sort_order' => 1,
                
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ========================================
            // 🚀 PAKET PROFESSIONAL - Untuk ISP Menengah
            // ========================================
            [
                'name' => 'Professional (30 Hari)',
                'slug' => 'professional-30',
                'description' => 'Paket professional untuk ISP menengah dengan masa aktif 30 hari',
                
                'price' => 150000,
                'active_days' => 30,
                
                // Limit
                'max_customers' => 500,
                'max_invoices' => 6000,
                'max_users' => 10,
                'max_locations' => 5,
                
                // Support
                'email_support' => 1,
                'whatsapp_support' => 1,
                'custom_branding' => 1,
                'multi_user_access' => 1,
                
                // Feature Flags - Fitur Lengkap (Kecuali ACS)
                'feature_free_subdomain' => 1,          // ✅ Free Subdomain
                'feature_custom_domain' => 1,           // ✅ Custom Domain
                'feature_maps_interaktif' => 1,         // ✅ Maps Interaktif
                'feature_dynamic_forwarding' => 1,      // ✅ Dynamic Forwarding ONT
                'feature_realtime_monitoring' => 1,     // ✅ Monitoring Realtime
                'feature_radius' => 1,                  // ✅ Radius
                'feature_acs' => 0,                     // ❌ ACS (Enterprise only)
                'feature_android_app' => 1,             // ✅ Android App
                'feature_payment_gateways' => 1,        // ✅ Payment Gateway (multiple)
                'feature_api_access' => 1,              // ✅ API Access (limited)
                'feature_custom_landing_page' => 1,     // ✅ Custom Landing Page (advanced)
                'feature_customer_portal' => 1,         // ✅ Customer Portal
                'feature_whatsapp_gateway' => 1,        // ✅ WhatsApp Gateway (500 msg/month)
                
                // Flags
                'requires_manual_approval' => 1,
                'is_popular' => 1, // PALING POPULER
                'is_active' => 1,
                'sort_order' => 2,
                
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // ========================================
            // 💎 PAKET ENTERPRISE - Untuk ISP Besar
            // ========================================
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Paket enterprise untuk ISP besar dengan SEMUA fitur lengkap',
                
                'price' => 3500000,
                'active_days' => 30,
                
                // Limit (0 = Unlimited)
                'max_customers' => 0,
                'max_invoices' => 0,
                'max_users' => 0,
                'max_locations' => 0,
                
                // Support
                'email_support' => 1,
                'whatsapp_support' => 1,
                'custom_branding' => 1,
                'multi_user_access' => 1,
                
                // Feature Flags - SEMUA FITUR AKTIF
                'feature_free_subdomain' => 1,          // ✅ Free Subdomain
                'feature_custom_domain' => 1,           // ✅ Custom Domain
                'feature_maps_interaktif' => 1,         // ✅ Maps Interaktif
                'feature_dynamic_forwarding' => 1,      // ✅ Dynamic Forwarding ONT
                'feature_realtime_monitoring' => 1,     // ✅ Monitoring Realtime
                'feature_radius' => 1,                  // ✅ Radius
                'feature_acs' => 1,                     // ✅ ACS
                'feature_android_app' => 1,             // ✅ Android App
                'feature_payment_gateways' => 1,        // ✅ Payment Gateway (unlimited)
                'feature_api_access' => 1,              // ✅ API Access (unlimited)
                'feature_custom_landing_page' => 1,     // ✅ Custom Landing Page (full custom)
                'feature_customer_portal' => 1,         // ✅ Customer Portal
                'feature_whatsapp_gateway' => 1,        // ✅ WhatsApp Gateway (unlimited)
                
                // Flags
                'requires_manual_approval' => 1,
                'is_popular' => 0,
                'is_active' => 1,
                'sort_order' => 3,
                
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
        // ========================================
        // 📋 RINGKASAN FITUR PER PAKET
        // ========================================
        /*
        
        TRIAL (Gratis):
        - ✅ Free Subdomain
        - ✅ 10 Customers max
        
        STARTER (Rp 50.000):
        - ✅ Free Subdomain
        - ✅ Maps Interaktif
        - ✅ Monitoring Realtime
        - ✅ Android App
        - ✅ 100 Customers max
        
        PROFESSIONAL (Rp 150.000):
        - ✅ Free Subdomain
        - ✅ Custom Domain
        - ✅ Maps Interaktif
        - ✅ Dynamic Forwarding ONT
        - ✅ VPN API
        - ✅ Monitoring Realtime
        - ✅ Radius
        - ✅ Android App
        - ✅ 500 Customers max
        
        ENTERPRISE (Rp 3.500.000):
        - ✅ SEMUA FITUR
        - ✅ Unlimited Customers
        
        */
    }
}
