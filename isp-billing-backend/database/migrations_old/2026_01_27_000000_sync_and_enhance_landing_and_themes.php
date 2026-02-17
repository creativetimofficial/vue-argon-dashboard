<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            // Hero Enhancements
            if (!Schema::hasColumn('landing_pages', 'hero_gradient_from')) {
                $table->string('hero_gradient_from')->nullable()->after('hero_image');
            }
            if (!Schema::hasColumn('landing_pages', 'hero_gradient_to')) {
                $table->string('hero_gradient_to')->nullable()->after('hero_gradient_from');
            }
            
            // Pricing Plans (JSON)
            if (!Schema::hasColumn('landing_pages', 'pricing_plans')) {
                $table->text('pricing_plans')->nullable()->after('show_pricing');
            }
            
            // Footer & Company Info
            if (!Schema::hasColumn('landing_pages', 'footer_company_name')) {
                $table->string('footer_company_name')->nullable()->after('contact_info');
            }
            if (!Schema::hasColumn('landing_pages', 'footer_copyright')) {
                $table->string('footer_copyright')->nullable()->after('footer_company_name');
            }
            if (!Schema::hasColumn('landing_pages', 'footer_social_links')) {
                $table->text('footer_social_links')->nullable()->after('footer_copyright');
            }
            
            // ISP ID should be nullable for main landing page
            $table->foreignId('isp_id')->nullable()->change();
        });

        Schema::table('isp_themes', function (Blueprint $table) {
            if (!Schema::hasColumn('isp_themes', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('is_active');
            }
            
            // Add missing branding fields if any
            if (!Schema::hasColumn('isp_themes', 'name')) {
                $table->string('name')->nullable()->after('system_name');
            }
            
            // Layout & UI Enhancements
            if (!Schema::hasColumn('isp_themes', 'border_radius')) {
                $table->string('border_radius', 20)->nullable()->after('font_size');
            }
            if (!Schema::hasColumn('isp_themes', 'button_style')) {
                $table->string('button_style', 20)->nullable()->after('border_radius');
            }
            if (!Schema::hasColumn('isp_themes', 'card_shadow')) {
                $table->string('card_shadow', 50)->nullable()->after('button_style');
            }
            if (!Schema::hasColumn('isp_themes', 'custom_js')) {
                $table->text('custom_js')->nullable()->after('custom_css');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn([
                'hero_gradient_from', 
                'hero_gradient_to', 
                'pricing_plans', 
                'footer_company_name', 
                'footer_copyright', 
                'footer_social_links'
            ]);
        });

        Schema::table('isp_themes', function (Blueprint $table) {
            $table->dropColumn(['is_default', 'name', 'border_radius', 'button_style', 'card_shadow', 'custom_js']);
        });
    }
};
