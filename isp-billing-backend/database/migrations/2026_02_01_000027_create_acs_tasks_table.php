<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acs_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acs_device_id')->constrained()->onDelete('cascade');
            
            $table->enum('task_type', ['firmware_upgrade', 'config_change', 'reboot', 'factory_reset']);
            $table->text('parameters')->nullable(); // JSON
            
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->text('result')->nullable();
            $table->text('error_message')->nullable();
            
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['acs_device_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acs_tasks');
    }
};
