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
            $table->foreignId('isp_id')->constrained('isps')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('isp_services')->onDelete('restrict');
            $table->string('reference')->unique(); // orderbaru-20260120195017
            $table->enum('status', ['pending', 'approved', 'active', 'cancelled', 'terminated'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->decimal('price', 15, 2)->default(0);
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annual', 'annual'])->default('monthly');
            
            // Domain selection
            $table->enum('domain_type', ['subdomain', 'custom'])->default('subdomain');
            $table->string('domain')->nullable(); // irvan.bayarinternet.com or custom domain
            $table->string('subdomain')->nullable(); // irvan
            
            // Service details (after activation)
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('server_address')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('l2tp_config')->nullable();
            $table->text('sstp_config')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('auto_renew')->default(false);
            
            // Dates
            $table->date('start_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            $table->index('isp_id');
            $table->index('service_id');
            $table->index('status');
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isp_orders');
    }
};
