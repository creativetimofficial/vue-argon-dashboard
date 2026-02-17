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
        // Delete legacy payment gateways
        DB::table('payment_gateways')
            ->whereIn('gateway_name', ['2Checkout', 'Razorpay', 'Bank_Transfer', 'Manual Bank Transfer', 'Bank Transfer (BCA)'])
            ->delete();
            
        // Also check by slug if any
        DB::table('payment_gateways')
            ->whereIn('slug', ['bank-transfer-bca', '2checkout', 'razorpay'])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse a deletion without knowing the exact settings
    }
};
