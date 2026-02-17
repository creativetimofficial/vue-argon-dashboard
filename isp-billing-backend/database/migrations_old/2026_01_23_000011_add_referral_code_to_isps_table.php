<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('isps', function (Blueprint $table) {
            $table->string('referral_code', 10)->unique()->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('isps', function (Blueprint $table) {
            $table->dropColumn('referral_code');
        });
    }
};
