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
            // Modify enum to include 'unpaid'
            $table->enum('payment_status', ['pending', 'paid', 'unpaid', 'failed', 'refunded'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('isp_orders', function (Blueprint $table) {
            // Revert back to original enum (assuming it was stricter)
            // Note: This might fail if there are 'unpaid' records
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->change();
        });
    }
};
