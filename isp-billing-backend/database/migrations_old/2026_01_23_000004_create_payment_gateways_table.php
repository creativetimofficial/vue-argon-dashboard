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
            $table->foreignId('isp_id')->constrained('isps')->onDelete('cascade');
            $table->enum('gateway_type', ['midtrans', 'xendit', 'paypal', 'stripe', 'bank_transfer'])->default('midtrans');
            $table->string('gateway_name', 100);
            $table->text('config')->nullable(); // JSON for API keys, etc.
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
