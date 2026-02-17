<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('isps', function (Blueprint $table) {
            // Domain Management
            $table->string('subdomain', 50)->unique()->nullable()->after('email');
            $table->string('custom_domain', 255)->unique()->nullable()->after('subdomain');
            $table->boolean('custom_domain_verified')->default(false)->after('custom_domain');
            
            // Location for Maps
            $table->decimal('latitude', 10, 8)->nullable()->after('postal_code');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('map_address', 500)->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('isps', function (Blueprint $table) {
            $table->dropColumn([
                'subdomain',
                'custom_domain',
                'custom_domain_verified',
                'latitude',
                'longitude',
                'map_address'
            ]);
        });
    }
};
