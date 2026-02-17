<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            // Font Settings
            if (!Schema::hasColumn('landing_pages', 'font_family')) {
                $table->string('font_family', 50)->default('Inter')->after('is_active');
            }
            if (!Schema::hasColumn('landing_pages', 'font_size_base')) {
                $table->string('font_size_base', 10)->default('16px')->after('font_family');
            }
            
            // Color Palette
            if (!Schema::hasColumn('landing_pages', 'color_primary')) {
                $table->string('color_primary', 20)->default('#667eea')->after('font_size_base');
            }
            if (!Schema::hasColumn('landing_pages', 'color_secondary')) {
                $table->string('color_secondary', 20)->default('#764ba2')->after('color_primary');
            }
            if (!Schema::hasColumn('landing_pages', 'color_accent')) {
                $table->string('color_accent', 20)->nullable()->after('color_secondary');
            }
            if (!Schema::hasColumn('landing_pages', 'color_text')) {
                $table->string('color_text', 20)->default('#1a202c')->after('color_accent');
            }
            if (!Schema::hasColumn('landing_pages', 'color_background')) {
                $table->string('color_background', 20)->default('#ffffff')->after('color_text');
            }
            
            // Layout Settings
            if (!Schema::hasColumn('landing_pages', 'spacing_scale')) {
                $table->decimal('spacing_scale', 3, 2)->default(1.00)->after('color_background');
            }
            if (!Schema::hasColumn('landing_pages', 'border_radius_base')) {
                $table->string('border_radius_base', 10)->default('12px')->after('spacing_scale');
            }
            if (!Schema::hasColumn('landing_pages', 'button_style')) {
                $table->string('button_style', 20)->default('rounded')->after('border_radius_base');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn([
                'font_family',
                'font_size_base',
                'color_primary',
                'color_secondary',
                'color_accent',
                'color_text',
                'color_background',
                'spacing_scale',
                'border_radius_base',
                'button_style'
            ]);
        });
    }
};
