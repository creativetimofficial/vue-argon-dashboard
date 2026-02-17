<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isp_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->enum('billing_cycle', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'])->default('monthly');
            $table->integer('trial_days')->default(0);
            $table->boolean('manual_approval')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isp_services');
    }
};
