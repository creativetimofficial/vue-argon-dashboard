<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            // Change hero_image from VARCHAR to LONGTEXT to support base64 images
            $table->longText('hero_image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->string('hero_image', 255)->nullable()->change();
        });
    }
};
