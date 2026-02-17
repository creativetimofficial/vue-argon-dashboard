<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('isp_themes', function (Blueprint $table) {
            // Button Colors
            $table->string('button_primary_color', 7)->default('#2dce89')->after('accent_color');
            $table->string('button_secondary_color', 7)->default('#11cdef')->after('button_primary_color');
            
            // Link & Text Colors
            $table->string('link_color', 7)->default('#5e72e4')->after('button_secondary_color');
            $table->string('text_primary_color', 7)->default('#344767')->after('link_color');
            $table->string('text_secondary_color', 7)->default('#8392ab')->after('text_primary_color');
            
            // Background Colors
            $table->string('sidebar_bg_color', 7)->default('#ffffff')->after('background_color');
            $table->string('navbar_bg_color', 7)->default('#ffffff')->after('sidebar_bg_color');
            $table->string('dashboard_bg_color', 7)->default('#f8f9fa')->after('navbar_bg_color');
            
            // Background Images
            $table->text('login_bg_image')->nullable()->after('logo_dark_url');
            $table->string('register_bg_image')->nullable()->after('login_bg_image');
            
            // Branding
            $table->boolean('use_custom_logo')->default(false)->after('register_bg_image');
            $table->string('company_name', 100)->nullable()->after('use_custom_logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('isp_themes', function (Blueprint $table) {
            $table->dropColumn([
                'button_primary_color',
                'button_secondary_color',
                'link_color',
                'text_primary_color',
                'text_secondary_color',
                'sidebar_bg_color',
                'navbar_bg_color',
                'dashboard_bg_color',
                'login_bg_image',
                'register_bg_image',
                'use_custom_logo',
                'company_name',
            ]);
        });
    }
};
