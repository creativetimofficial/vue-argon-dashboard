<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('ont_device_id')->nullable()->constrained()->onDelete('set null');
            
            $table->enum('log_type', ['bandwidth', 'connection', 'error', 'system']);
            $table->string('metric_name', 100);
            $table->decimal('metric_value', 15, 2);
            $table->string('unit', 20)->nullable(); // Mbps, GB, ms, etc
            
            $table->text('details')->nullable();
            $table->timestamp('logged_at');
            
            $table->index(['isp_id', 'logged_at']);
            $table->index(['customer_id', 'logged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_logs');
    }
};
