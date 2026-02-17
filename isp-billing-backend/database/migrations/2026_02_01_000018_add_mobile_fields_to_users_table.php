<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mobile App Support
            $table->string('fcm_token', 255)->nullable()->after('remember_token');
            $table->string('device_id', 255)->nullable()->after('fcm_token');
            $table->enum('device_type', ['android', 'ios', 'web'])->nullable()->after('device_id');
            $table->timestamp('last_app_login')->nullable()->after('device_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fcm_token',
                'device_id',
                'device_type',
                'last_app_login'
            ]);
        });
    }
};
