<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            // Change gateway_type from enum to string to allow 'local', 'international', etc.
            $table->string('gateway_type')->change();
            
            // Ensure gateway_name is a string (it should be already)
            $table->string('gateway_name')->change();

            // Drop redundant columns if they were added in the previous failed attempts to "sync"
            if (Schema::hasColumn('payment_gateways', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('payment_gateways', 'type')) {
                $table->dropColumn('type');
            }
        });
    }

    public function down(): void
    {
        // No need for down for this emergency fix
    }
};
