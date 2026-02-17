<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bandwidth_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            
            $table->date('usage_date');
            $table->bigInteger('download_bytes')->default(0);
            $table->bigInteger('upload_bytes')->default(0);
            $table->bigInteger('total_bytes')->default(0);
            
            $table->timestamps();
            
            $table->unique(['customer_id', 'usage_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bandwidth_usage');
    }
};
