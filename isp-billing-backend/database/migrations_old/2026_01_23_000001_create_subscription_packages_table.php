<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 12, 2);
            $table->decimal('quarterly_price', 12, 2)->nullable();
            $table->decimal('semi_annual_price', 12, 2)->nullable();
            $table->decimal('annual_price', 12, 2)->nullable();

            // Features
            $table->integer('max_customers')->default(0);
            $table->integer('max_invoices')->default(0);
            $table->boolean('email_support')->default(true);
            $table->boolean('whatsapp_support')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('api_access')->default(false);
            $table->boolean('multi_user_access')->default(false);
            $table->text('features')->nullable(); // JSON

            // Status
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};
