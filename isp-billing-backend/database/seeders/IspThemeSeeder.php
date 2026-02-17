<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IspTheme;

class IspThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default theme for all ISPs (isp_id = null)
        IspTheme::updateOrCreate(
            ['isp_id' => null, 'is_default' => true],
            [
                'name' => 'Argon Green Theme',
                
                // Logos (from original migration)
                'logo_url' => 'https://demos.creative-tim.com/argon-dashboard-pro/assets/img/logo-ct.png',
                'logo_dark_url' => 'https://demos.creative-tim.com/argon-dashboard-pro/assets/img/logo-ct-dark.png',
                'favicon_url' => '',
                
                // Main Colors (from original migration - use primary_color, not color_primary)
                'primary_color' => '#2dce89',
                'secondary_color' => '#11cdef',
                'accent_color' => '#5e72e4',
                'background_color' => '#f8f9fa',
                'text_color' => '#344767',
                
                // New Extended Fields (from new migration)
                'button_primary_color' => '#2dce89',
                'button_secondary_color' => '#11cdef',
                'link_color' => '#5e72e4',
                'text_primary_color' => '#344767',
                'text_secondary_color' => '#8392ab',
                'navbar_bg_color' => '#ffffff',
                'dashboard_bg_color' => '#f8f9fa',
                
                // Typography (from original migration)
                'font_family' => 'Open Sans',
                'heading_font' => null,
                
                // Layout (from original migration)
                'sidebar_position' => 'left',
                'dark_mode' => false,
                
                // Branding (from new migration)
                'use_custom_logo' => false,
                'company_name' => 'ISP Billing Pro',
                
                // Custom CSS (from original migration)
                'custom_css' => '',
                
                // Status
                'is_active' => true,
                'is_default' => true,
            ]
        );
    }
}
