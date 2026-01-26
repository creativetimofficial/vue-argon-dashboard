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
            // Make service_id nullable because an order can be for a package instead
            $table->foreignId('service_id')->nullable()->change();
            
            // Add subscription_package_id column
            $table->foreignId('subscription_package_id')
                ->nullable()
                ->after('service_id')
                ->constrained('subscription_packages')
                ->onDelete('restrict');
                
            $table->index('subscription_package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('isp_orders', function (Blueprint $table) {
            $table->dropForeign(['subscription_package_id']);
            $table->dropColumn('subscription_package_id');
            $table->foreignId('service_id')->nullable(false)->change();
        });
    }
};
