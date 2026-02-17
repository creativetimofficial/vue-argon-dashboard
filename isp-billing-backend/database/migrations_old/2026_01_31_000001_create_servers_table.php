<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->string('domain')->nullable();
            $table->string('username')->nullable()->comment('Mikrotik API User');
            $table->string('password')->nullable()->comment('Mikrotik API Password');
            $table->integer('api_port')->default(8728);
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('capacity')->default(1000);
            $table->integer('current_users')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
