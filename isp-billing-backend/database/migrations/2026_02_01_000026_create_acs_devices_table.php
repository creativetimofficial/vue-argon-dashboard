<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acs_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('device_id', 100)->unique(); // TR-069 Device ID
            $table->string('serial_number', 100);
            $table->string('manufacturer', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('software_version', 50)->nullable();
            
            // Connection Info
            $table->string('connection_request_url', 500)->nullable();
            $table->string('connection_request_username', 100)->nullable();
            $table->text('connection_request_password')->nullable();
            
            // Status
            $table->enum('status', ['online', 'offline', 'provisioning'])->default('offline');
            $table->timestamp('last_inform')->nullable();
            
            $table->timestamps();
            
            $table->index(['isp_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acs_devices');
    }
};
