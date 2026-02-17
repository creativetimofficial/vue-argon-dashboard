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
        Schema::table('invoices', function (Blueprint $table) {
            // Polymorphic relation
            $table->string('billable_type')->nullable()->after('invoice_number');
            $table->unsignedBigInteger('billable_id')->nullable()->after('billable_type');
            
            // Rename relations if needed, or add new ones
            if (Schema::hasColumn('invoices', 'isp_order_id') && !Schema::hasColumn('invoices', 'subscription_id')) {
                $table->renameColumn('isp_order_id', 'subscription_id');
            } elseif (!Schema::hasColumn('invoices', 'subscription_id')) {
                $table->foreignId('subscription_id')->nullable()->constrained('isp_orders')->onDelete('set null');
            }

            // Rename columns to match model/code
            if (Schema::hasColumn('invoices', 'invoice_date') && !Schema::hasColumn('invoices', 'issue_date')) {
                $table->renameColumn('invoice_date', 'issue_date');
            }
            if (Schema::hasColumn('invoices', 'status') && !Schema::hasColumn('invoices', 'payment_status')) {
                // We keep status as general status, add payment_status
                $table->enum('payment_status', ['unpaid', 'paid', 'partial', 'refunded'])->default('unpaid')->after('total');
            }
            
            // Add periods
            $table->date('period_start')->nullable()->after('paid_at');
            $table->date('period_end')->nullable()->after('period_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['billable_type', 'billable_id', 'payment_status', 'period_start', 'period_end']);
            
            if (Schema::hasColumn('invoices', 'subscription_id')) {
                $table->renameColumn('subscription_id', 'isp_order_id');
            }
            
            if (Schema::hasColumn('invoices', 'issue_date')) {
                $table->renameColumn('issue_date', 'invoice_date');
            }
        });
    }
};
