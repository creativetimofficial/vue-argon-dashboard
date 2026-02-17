<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radius_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            
            $table->string('username', 100)->unique();
            $table->string('password', 255); // Encrypted
            $table->string('static_ip', 45)->nullable();
            
            // Bandwidth Limits
            $table->integer('download_limit_kbps')->nullable();
            $table->integer('upload_limit_kbps')->nullable();
            
            // Session Limits
            $table->integer('session_timeout')->nullable(); // seconds
            $table->integer('idle_timeout')->nullable(); // seconds
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radius_accounts');
    }
};
