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
            DB::statement("ALTER TABLE isp_orders MODIFY COLUMN status ENUM('pending_payment', 'processing', 'active', 'trial', 'superseded', 'suspended', 'cancelled', 'expired', 'failed') DEFAULT 'pending_payment'");
        });
    }

    public function down(): void
    {
        Schema::table('isp_orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE isp_orders MODIFY COLUMN status ENUM('pending_payment', 'processing', 'active', 'trial', 'suspended', 'cancelled', 'expired', 'failed') DEFAULT 'pending_payment'");
        });
    }
};
