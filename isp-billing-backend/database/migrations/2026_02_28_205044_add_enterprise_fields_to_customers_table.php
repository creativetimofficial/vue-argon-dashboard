<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('ont_serial_number', 100)->nullable()->after('address');
            $table->string('ip_address', 45)->nullable()->after('ont_serial_number');
            $table->string('mac_address', 100)->nullable()->after('ip_address');
            $table->string('mikrotik_username', 100)->nullable()->after('mac_address');
            $table->string('mikrotik_password', 100)->nullable()->after('mikrotik_username');
            $table->string('id_number', 50)->nullable()->after('phone');
            $table->string('id_type', 20)->default('KTP')->after('id_number');
            $table->text('installation_address')->nullable()->after('address');
            $table->text('billing_address')->nullable()->after('installation_address');
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->date('installation_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'ont_serial_number', 'ip_address', 'mac_address', 
                'mikrotik_username', 'mikrotik_password', 'id_number', 
                'id_type', 'installation_address', 'billing_address',
                'city', 'province', 'postal_code', 'installation_date'
            ]);
        });
    }
};
