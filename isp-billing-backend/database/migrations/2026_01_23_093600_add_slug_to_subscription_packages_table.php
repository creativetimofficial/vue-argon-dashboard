<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom slug sudah ada, baris berikut dikomentari agar migrasi tidak error
        // Schema::table('subscription_packages', function (Blueprint $table) {
        //     $table->string('slug')->unique()->after('id');
        // });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
