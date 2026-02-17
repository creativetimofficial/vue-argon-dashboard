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
        Schema::table('isp_orders', function (Blueprint $table) {
            // Update status enum to include payment lifecycle states
            DB::statement("ALTER TABLE isp_orders MODIFY COLUMN status ENUM('pending_payment', 'processing', 'active', 'trial', 'suspended', 'cancelled', 'expired', 'failed') DEFAULT 'pending_payment'");
            
            // Update payment_status enum
            DB::statement("ALTER TABLE isp_orders MODIFY COLUMN payment_status ENUM('unpaid', 'pending', 'paid', 'partial', 'refunded', 'expired') DEFAULT 'unpaid'");
            
            // Add new columns for payment lifecycle
            $table->timestamp('payment_expired_at')->nullable()->after('payment_status');
            $table->string('payment_gateway', 50)->nullable()->after('payment_expired_at');
            $table->string('payment_reference', 100)->nullable()->after('payment_gateway');
            $table->boolean('domain_active')->default(false)->after('payment_reference');
            $table->integer('retry_count')->default(0)->after('domain_active');
            $table->timestamp('last_retry_at')->nullable()->after('retry_count');
            
            // Add soft deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('isp_orders', function (Blueprint $table) {
            // Revert status enum to original
            DB::statement("ALTER TABLE isp_orders MODIFY COLUMN status ENUM('pending', 'active', 'trial', 'suspended', 'cancelled') DEFAULT 'pending'");
            
            // Revert payment_status enum
            DB::statement("ALTER TABLE isp_orders MODIFY COLUMN payment_status ENUM('unpaid', 'paid', 'partial', 'refunded') DEFAULT 'unpaid'");
            
            // Drop new columns
            $table->dropColumn([
                'payment_expired_at',
                'payment_gateway',
                'payment_reference',
                'domain_active',
                'retry_count',
                'last_retry_at'
            ]);
            
            $table->dropSoftDeletes();
        });
    }
};
