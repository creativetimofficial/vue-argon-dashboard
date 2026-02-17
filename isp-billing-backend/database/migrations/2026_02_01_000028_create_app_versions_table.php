<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            
            $table->string('version_name', 20); // 1.0.0
            $table->integer('version_code'); // 1
            $table->enum('platform', ['android', 'ios']);
            
            $table->text('download_url');
            $table->text('changelog')->nullable();
            
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_latest')->default(false);
            
            $table->timestamps();
            
            $table->unique(['version_code', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
