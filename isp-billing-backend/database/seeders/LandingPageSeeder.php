<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPage;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        // Use updateOrCreate to prevent duplicates
        // This will update existing landing page or create new one if not exists
        LandingPage::updateOrCreate(
            ['isp_id' => null], // Find by: Main landing page (null = untuk semua ISP)
            [
            // Update/Create with these values:
            
            // ========================================
            // 🎯 HERO SECTION - Bagian Utama Landing Page
            // ========================================
            'hero_badge_text' => '⭐ Platform #1 untuk ISP',
            // Contoh lain:
            // '🚀 Solusi Terbaik untuk ISP'
            // '🏆 Platform ISP Terbaik 2026'
            // '💎 Premium ISP Solution'
            // '✨ Dipercaya 500+ ISP'
            
            'hero_title' => 'Platform Billing ISP #1 di Indonesia',
            // Judul besar di hero section
            
            'hero_subtitle' => 'Kelola bisnis ISP Anda dengan mudah. Integrasi Mikrotik otomatis, payment gateway lengkap, dan sistem billing yang powerful. Dipercaya oleh 500+ ISP di seluruh Indonesia.',
            // Deskripsi di bawah judul
            
            'hero_image' => null,
            // URL gambar hero (kosongkan untuk placeholder default)
            
            'hero_cta_text' => 'Coba Gratis 14 Hari',
            // Teks tombol utama
            
            'hero_cta_link' => '/register',
            // Link tombol utama
            
            'hero_gradient_from' => '#2dce89',
            'hero_gradient_to' => '#11cdef',
            // Warna gradient background hero (Green to Cyan - Argon Success to Info)
            
            // ========================================
            // ✨ FEATURES SECTION - Fitur-Fitur Unggulan
            // ========================================
            'show_features' => true,
            'features' => [
                [
                    'icon' => 'ni ni-credit-card',
                    'title' => 'Payment Gateway',
                    'description' => 'Integrasi otomatis dengan Midtrans & Xendit untuk pembayaran realtime'
                ],
                [
                    'icon' => 'ni ni-world-2',
                    'title' => 'Mikrotik Integration',
                    'description' => 'Kelola bandwidth dan isolasi pelanggan langsung dari sistem'
                ],
                [
                    'icon' => 'ni ni-money-coins',
                    'title' => 'Billing Otomatis',
                    'description' => 'Generate invoice otomatis dan kirim ke pelanggan via email'
                ],
                [
                    'icon' => 'ni ni-single-02',
                    'title' => 'Manajemen Pelanggan',
                    'description' => 'Database pelanggan lengkap dengan riwayat pembayaran'
                ],
                [
                    'icon' => 'ni ni-settings-gear-65',
                    'title' => 'Ticketing System',
                    'description' => 'Kelola keluhan dan permintaan pelanggan dengan mudah'
                ],
                [
                    'icon' => 'ni ni-chart-bar-32',
                    'title' => 'Laporan Lengkap',
                    'description' => 'Dashboard analytics dan laporan keuangan real-time'
                ],
            ],
            // Icon dari Font Awesome: https://fontawesome.com/icons
            
            // ========================================
            // 💰 PRICING SECTION - Harga Paket (Landing Page)
            // ========================================
            // CATATAN: Ini hanya untuk tampilan di landing page
            // Harga sebenarnya ada di SubscriptionPackageSeeder.php
            'show_pricing' => true,
            'pricing_plans' => [
                [
                    'name' => 'Starter',
                    'price' => 299000,
                    'period' => 'bulan',
                    'isPopular' => false,
                    'features' => "Maksimal 100 Pelanggan\n1 User Admin\n2 Mikrotik Router\nPayment Gateway\nEmail Notification\nSupport via Email"
                ],
                [
                    'name' => 'Professional',
                    'price' => 599000,
                    'period' => 'bulan',
                    'isPopular' => true, // Badge "PALING POPULER"
                    'features' => "Maksimal 500 Pelanggan\n3 User Admin\n5 Mikrotik Router\nPayment Gateway\nSMS & Email Notification\nPriority Support\nCustom Domain"
                ],
                [
                    'name' => 'Enterprise',
                    'price' => 1299000,
                    'period' => 'bulan',
                    'isPopular' => false,
                    'features' => "Unlimited Pelanggan\nUnlimited User\nUnlimited Router\nPayment Gateway\nSMS & Email & WhatsApp\n24/7 Support\nCustom Domain\nAPI Access"
                ],
            ],
            
            // ========================================
            // 💬 TESTIMONIALS - Testimoni Pelanggan
            // ========================================
            'show_testimonials' => true,
            'testimonials' => [
                [
                    'name' => 'Budi Santoso',
                    'company' => 'NetSpeed ISP',
                    'text' => 'Sistem billing ini sangat membantu kami mengelola 300+ pelanggan dengan efisien. Fitur auto-billing menghemat banyak waktu!',
                    'avatar' => null
                ],
                [
                    'name' => 'Siti Rahayu',
                    'company' => 'FastNet Indonesia',
                    'text' => 'Integrasi dengan Mikrotik sangat smooth. Customer support juga responsif membantu setup awal.',
                    'avatar' => null
                ],
                [
                    'name' => 'Ahmad Wijaya',
                    'company' => 'SkyLink ISP',
                    'text' => 'Dashboard analytics-nya sangat membantu untuk monitoring bisnis. Highly recommended!',
                    'avatar' => null
                ],
            ],
            
            // ========================================
            // 🏢 LOGOS - Partner/Integration Logos
            // ========================================
            'show_logos' => true,
            'logos' => [
                ['name' => 'Midtrans', 'url' => 'https://via.placeholder.com/120x40?text=Midtrans'],
                ['name' => 'Xendit', 'url' => 'https://via.placeholder.com/120x40?text=Xendit'],
                ['name' => 'Mikrotik', 'url' => 'https://via.placeholder.com/120x40?text=Mikrotik'],
                ['name' => 'WhatsApp', 'url' => 'https://via.placeholder.com/120x40?text=WhatsApp'],
            ],
            // Ganti dengan URL logo asli
            
            // ========================================
            // ❓ FAQs - Pertanyaan yang Sering Diajukan
            // ========================================
            'show_faqs' => true,
            'faqs' => [
                [
                    'question' => 'Apakah ada trial gratis?',
                    'answer' => 'Ya, kami menyediakan trial gratis 14 hari untuk semua paket tanpa perlu kartu kredit.'
                ],
                [
                    'question' => 'Bagaimana cara integrasi dengan Mikrotik?',
                    'answer' => 'Sistem kami sudah terintegrasi dengan Mikrotik API. Anda hanya perlu memasukkan IP dan kredensial router Anda.'
                ],
                [
                    'question' => 'Apakah data saya aman?',
                    'answer' => 'Sangat aman. Kami menggunakan enkripsi SSL dan backup otomatis setiap hari.'
                ],
                [
                    'question' => 'Bisakah upgrade/downgrade paket?',
                    'answer' => 'Tentu! Anda bisa upgrade atau downgrade paket kapan saja dari dashboard.'
                ],
                [
                    'question' => 'Apakah ada biaya setup?',
                    'answer' => 'Tidak ada biaya setup. Anda hanya membayar biaya langganan bulanan sesuai paket yang dipilih.'
                ],
            ],
            
            // ========================================
            // 📞 CONTACT INFO - Informasi Kontak
            // ========================================
            'show_contact' => true,
            'contact_info' => [
                'email' => 'support@ispbilling.com',
                'phone' => '+62 812-3456-7890',
                'address' => 'Jakarta, Indonesia'
            ],
            // PENTING: Ubah dengan kontak Anda yang sebenarnya!
            
            // ========================================
            // 🔗 FOOTER - Footer & Social Media
            // ========================================
            'footer_company_name' => 'ISP Billing System',
            'footer_copyright' => '© 2026 ISP Billing System. All rights reserved.',
            'footer_social_links' => [
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'instagram' => 'https://instagram.com',
                'linkedin' => 'https://linkedin.com'
            ],
            // Ubah dengan link social media Anda
            
            // ========================================
            // 🔍 SEO - Meta Tags untuk Google
            // ========================================
            'meta_title' => 'ISP Billing System - Platform Manajemen ISP Terlengkap',
            'meta_description' => 'Sistem billing ISP terlengkap dengan integrasi Mikrotik, payment gateway, dan fitur lengkap untuk mengelola bisnis ISP Anda.',
            'meta_keywords' => 'isp billing, mikrotik, payment gateway, sistem billing, manajemen isp',
            
            // ========================================
            // 🎨 STYLING - Warna & Font (Argon Green Theme)
            // ========================================
            'font_family' => 'Open Sans',
            // Font dari Google Fonts: Open Sans (Argon default), Inter, Poppins, Roboto, dll
            
            'font_size_base' => '16px',
            
            'color_primary' => '#2dce89',
            // Warna utama (tombol, link, dll) - Argon Success Green
            
            'color_secondary' => '#11cdef',
            // Warna sekunder (gradient, accent) - Argon Info Cyan
            
            'color_accent' => '#5e72e4',
            // Warna aksen - Argon Primary Blue
            
            'color_text' => '#344767',
            // Warna teks utama - Argon Heading Color
            
            'color_background' => '#ffffff',
            // Warna background
            
            'spacing_scale' => 1.1,
            // Skala spacing (1.0 = normal, 1.1 = lebih lega, 0.8 = lebih rapat)
            
            'border_radius_base' => '0.75rem',
            // Border radius untuk card, button, dll (Argon style)
            
            'button_style' => 'rounded',
            // Style button: rounded, square, pill
            
            'is_active' => true,
            ]
        );
    }
}
