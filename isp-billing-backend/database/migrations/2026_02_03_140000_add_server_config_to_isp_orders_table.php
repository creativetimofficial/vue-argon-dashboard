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
        Schema::table('isp_orders', function (Blueprint $table) {
            $table->string('server_address')->nullable()->after('password');
            $table->text('l2tp_config')->nullable()->after('server_address');
            $table->text('sstp_config')->nullable()->after('l2tp_config');
            // Adding other potential missing configs if needed to handle future scaling
             $table->text('ovpn_config')->nullable()->after('sstp_config');
             $table->text('wireguard_config')->nullable()->after('ovpn_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('isp_orders', function (Blueprint $table) {
            $table->dropColumn(['server_address', 'l2tp_config', 'sstp_config', 'ovpn_config', 'wireguard_config']);
        });
    }
};
