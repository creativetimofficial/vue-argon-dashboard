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
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->string('billable_type');
                $table->unsignedBigInteger('billable_id');
                $table->foreignId('isp_id')->constrained('isps')->onDelete('cascade');
                $table->foreignId('subscription_id')->nullable(); // Can be linked to isp_orders or other
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax', 15, 2)->default(0);
                $table->decimal('discount', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->enum('payment_status', ['unpaid', 'paid', 'partial', 'overdue', 'cancelled'])->default('unpaid');
                // We are adding payment_method in a later migration, but we can add it here to be safe if that one fails or is skipped
                //$table->string('payment_method')->nullable(); 
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->date('issue_date');
                $table->date('due_date');
                $table->timestamp('paid_at')->nullable();
                $table->date('period_start')->nullable();
                $table->date('period_end')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['billable_type', 'billable_id']);
                $table->index('payment_status');
            });
        }

        if (!Schema::hasTable('invoice_items')) {
             Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
                $table->string('description');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('amount', 15, 2)->default(0); // quantity * unit_price
                $table->decimal('total', 15, 2)->default(0); // after tax/discount if applied per item
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
