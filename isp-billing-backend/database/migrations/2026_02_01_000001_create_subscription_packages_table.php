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
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            
            // Simplified pricing - single price with duration
            $table->decimal('price', 12, 2);
            $table->integer('discount_percent')->default(0); // Added discount
            $table->integer('active_days')->nullable(); // Duration in days
            $table->integer('trial_days')->default(0); // Added trial days
            
            // Features
            $table->integer('max_customers')->default(0);
            $table->integer('max_invoices')->default(0);
            $table->integer('max_users')->nullable();
            $table->integer('max_locations')->nullable();
            $table->boolean('email_support')->default(true);
            $table->boolean('whatsapp_support')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('multi_user_access')->default(false);
            
            // Additional Features
            $table->boolean('feature_whitelabel')->default(false);
            $table->boolean('feature_priority_support')->default(false);
            $table->boolean('feature_analytics')->default(false);
            $table->boolean('feature_multi_currency')->default(false);
            $table->boolean('feature_automated_billing')->default(false);
            $table->text('features')->nullable(); // JSON for additional features
            
            // Flags
            $table->boolean('requires_manual_approval')->default(false);
            $table->boolean('is_popular')->default(false);
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
