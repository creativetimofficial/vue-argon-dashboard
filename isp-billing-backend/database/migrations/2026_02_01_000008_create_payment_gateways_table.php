<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_name', 100); // Midtrans, Xendit, PayPal, etc
            $table->string('slug', 100)->unique(); // midtrans, xendit, paypal
            $table->string('gateway_type', 50); // local, international
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            
            // API Credentials
            $table->string('api_key', 255)->nullable();
            $table->string('secret_key', 255)->nullable();
            $table->string('merchant_id', 100)->nullable();
            $table->string('client_id', 255)->nullable();
            
            // Configuration
            $table->json('settings')->nullable(); // Additional settings
            $table->json('supported_countries')->nullable(); // ["ID", "SG", "MY"]
            
            // Fees
            $table->decimal('transaction_fee', 10, 2)->default(0); // Percentage fee
            $table->decimal('fixed_fee', 10, 2)->default(0); // Fixed fee per transaction
            $table->string('currency', 3)->default('IDR');
            
            // Webhook
            $table->string('webhook_secret', 255)->nullable();
            $table->string('webhook_url', 255)->nullable();
            
            // Environment
            $table->boolean('sandbox_mode')->default(true);
            
            // UI
            $table->string('logo_url', 255)->nullable();
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
