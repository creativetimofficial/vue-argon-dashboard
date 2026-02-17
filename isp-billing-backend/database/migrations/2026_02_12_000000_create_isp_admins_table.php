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
        Schema::create('isp_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email'); // Not unique globally, allowing same email as users table
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('company_name');
            $table->text('company_address')->nullable();
            $table->string('subdomain')->unique(); // Unique identifier for tenant
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Index for faster lookups
            $table->index('email');
            $table->index('subdomain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('isp_admins');
    }
};
