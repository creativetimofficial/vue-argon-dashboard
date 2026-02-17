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
            $table->foreignId('isp_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name', 100);
            
            // Colors
            $table->string('primary_color', 7)->default('#3B82F6');
            $table->string('secondary_color', 7)->default('#10B981');
            $table->string('accent_color', 7)->default('#F59E0B');
            $table->string('background_color', 7)->default('#FFFFFF');
            $table->string('text_color', 7)->default('#1F2937');
            
            // Typography
            $table->string('font_family', 100)->default('Inter');
            $table->string('heading_font', 100)->nullable();
            
            // Layout
            $table->string('sidebar_position', 20)->default('left'); // left, right
            $table->boolean('dark_mode')->default(false);
            
            // Logo
            $table->text('logo_url')->nullable();
            $table->text('logo_dark_url')->nullable();
            $table->text('favicon_url')->nullable();
            
            // Custom CSS
            $table->text('custom_css')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isp_themes');
    }
};
