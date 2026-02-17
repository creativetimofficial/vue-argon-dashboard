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
            $table->enum('landing_page_template', ['sneat', 'modern', 'creative'])->default('sneat')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('isp_themes', function (Blueprint $table) {
            $table->dropColumn('landing_page_template');
        });
    }
};
