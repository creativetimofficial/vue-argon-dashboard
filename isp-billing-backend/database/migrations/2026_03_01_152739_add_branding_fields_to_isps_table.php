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
        Schema::table('isps', function (Blueprint $table) {
            $table->string('favicon', 255)->after('logo')->nullable();
            $table->string('theme_color', 50)->after('favicon')->default('primary');
            $table->json('notification_settings')->after('theme_color')->nullable();
            $table->string('wa_gateway_url', 255)->after('notification_settings')->nullable();
            $table->string('wa_api_key', 255)->after('wa_gateway_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('isps', function (Blueprint $table) {
            $table->dropColumn(['favicon', 'theme_color', 'notification_settings', 'wa_gateway_url', 'wa_api_key']);
        });
    }
};
