<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isp_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->constrained()->onDelete('cascade');
            $table->foreignId('isp_service_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subscription_package_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('server_id')->nullable()->constrained('servers')->onDelete('set null');
            $table->string('reference', 50)->unique();
            $table->enum('order_type', ['service', 'package'])->default('service');
            
            // Service details
            $table->string('service_name', 100);
            $table->decimal('price', 12, 2);
            $table->enum('billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'])->default('monthly');
            
            // Service credentials
            $table->string('username', 100)->nullable();
            $table->string('password', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            
            // Status
            $table->enum('status', ['pending', 'active', 'trial', 'suspended', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'partial', 'refunded'])->default('unpaid');
            $table->date('start_date')->nullable();
            $table->date('expired_date')->nullable();
            
            // Approval
            $table->boolean('requires_approval')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isp_orders');
    }
};
