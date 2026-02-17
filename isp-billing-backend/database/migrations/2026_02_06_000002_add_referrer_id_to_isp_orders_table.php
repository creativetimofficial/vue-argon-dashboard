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
        Schema::table('isp_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('referrer_id')->nullable()->after('isp_id');
            // We don't strictly enforce foreign key to 'isps' here to allow soft deletes or flexibility,
            // but usually it should strictly reference 'isps'. Let's add index for performance.
            $table->index('referrer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('isp_orders', function (Blueprint $table) {
            $table->dropColumn('referrer_id');
        });
    }
};
