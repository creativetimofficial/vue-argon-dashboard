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
            $table->string('domain_type')->nullable()->after('billing_cycle');
            $table->string('subdomain')->nullable()->after('domain_type');
            $table->string('domain')->nullable()->after('subdomain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('isp_orders', function (Blueprint $table) {
            $table->dropColumn(['domain_type', 'subdomain', 'domain']);
        });
    }
};
