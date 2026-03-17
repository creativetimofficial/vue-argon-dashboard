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
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->decimal('admin_fee', 15, 2)->default(0)->after('amount');
            $table->decimal('total_transfer', 15, 2)->default(0)->after('admin_fee');
            $table->string('xendit_disbursement_id')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['admin_fee', 'total_transfer', 'xendit_disbursement_id']);
        });
    }
};
