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
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->boolean('requires_manual_approval')->default(false)->after('is_active');
        });

        Schema::table('isp_services', function (Blueprint $table) {
            $table->boolean('requires_manual_approval')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn('requires_manual_approval');
        });

        Schema::table('isp_services', function (Blueprint $table) {
            $table->dropColumn('requires_manual_approval');
        });
    }
};
