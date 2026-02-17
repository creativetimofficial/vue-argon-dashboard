<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw SQL to ensure the enum is updated correctly, as standard schema builder might fail with Doctrine constraint
        DB::statement("ALTER TABLE isp_orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'unpaid', 'failed', 'refunded') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to the previous state (without 'unpaid')
        // Warning: This will cause data truncation if 'unpaid' records exist.
        DB::statement("ALTER TABLE isp_orders MODIFY COLUMN payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending'");
    }
};
