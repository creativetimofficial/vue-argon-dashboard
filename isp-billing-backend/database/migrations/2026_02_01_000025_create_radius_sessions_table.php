<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radius_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radius_account_id')->constrained()->onDelete('cascade');
            
            $table->string('session_id', 100)->unique();
            $table->string('nas_ip_address', 45);
            $table->string('framed_ip_address', 45)->nullable();
            
            $table->timestamp('start_time');
            $table->timestamp('stop_time')->nullable();
            $table->integer('session_duration')->nullable(); // seconds
            
            $table->bigInteger('input_octets')->default(0);
            $table->bigInteger('output_octets')->default(0);
            
            $table->string('terminate_cause', 50)->nullable();
            
            $table->index(['radius_account_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radius_sessions');
    }
};
