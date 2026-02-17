<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ont_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('serial_number', 100)->unique();
            $table->string('mac_address', 17)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('firmware_version', 50)->nullable();
            
            // Connection Info
            $table->string('ip_address', 45)->nullable();
            $table->integer('port')->nullable();
            $table->string('username', 100)->nullable();
            $table->text('password_encrypted')->nullable();
            
            // Status
            $table->enum('status', ['online', 'offline', 'error'])->default('offline');
            $table->timestamp('last_seen')->nullable();
            
            // Dynamic Forwarding Remote ONT
            $table->json('port_forwarding_rules')->nullable();
            
            $table->timestamps();
            
            $table->index(['isp_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ont_devices');
    }
};
