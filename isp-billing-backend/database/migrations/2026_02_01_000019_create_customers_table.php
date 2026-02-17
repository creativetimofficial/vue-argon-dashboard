<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->constrained()->onDelete('cascade');
            $table->string('customer_code', 50)->unique();
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->string('phone', 20);
            $table->text('address');
            
            // Location for Maps Interaktif
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Service Info
            $table->foreignId('service_plan_id')->nullable()->constrained('isp_services')->onDelete('set null');
            $table->enum('status', ['active', 'suspended', 'terminated'])->default('active');
            
            // Billing
            $table->decimal('balance', 12, 2)->default(0);
            $table->date('next_billing_date')->nullable();
            
            $table->timestamps();
            
            $table->index(['isp_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
