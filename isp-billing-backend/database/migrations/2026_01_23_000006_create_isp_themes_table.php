<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isp_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->nullable()->constrained('isps')->onDelete('cascade');

            // Branding
            $table->string('system_name')->default('ISP Billing System');
            $table->string('logo_light')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('favicon')->nullable();

            // Color Scheme
            $table->string('color_primary', 7)->default('#5e72e4');
            $table->string('color_secondary', 7)->default('#8392ab');
            $table->string('color_success', 7)->default('#2dce89');
            $table->string('color_danger', 7)->default('#f5365c');
            $table->string('color_warning', 7)->default('#fb6340');
            $table->string('color_info', 7)->default('#11cdef');

            // Sidebar Settings
            $table->string('sidebar_bg_color', 7)->default('#172b4d');
            $table->string('sidebar_text_color', 7)->default('#8898aa');
            $table->string('sidebar_active_color', 7)->default('#5e72e4');
            $table->enum('sidebar_type', ['transparent', 'white', 'dark'])->default('dark');

            // Typography
            $table->string('font_family')->default('Open Sans');
            $table->string('font_size', 10)->default('16px');

            // Layout Options
            $table->boolean('dark_mode_default')->default(false);
            $table->boolean('sidebar_mini')->default(false);
            $table->boolean('fixed_navbar')->default(true);

            // Custom CSS
            $table->text('custom_css')->nullable();

            // Additional theme settings (JSON)
            $table->text('additional_settings')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isp_themes');
    }
};
