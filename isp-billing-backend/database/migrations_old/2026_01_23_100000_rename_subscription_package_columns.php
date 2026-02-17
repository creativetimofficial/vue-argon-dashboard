<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->renameColumn('monthly_price', 'price_monthly');
            $table->renameColumn('annual_price', 'price_yearly');
            $table->renameColumn('quarterly_price', 'price_quarterly');
            $table->renameColumn('semi_annual_price', 'price_semi_annual');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->renameColumn('price_monthly', 'monthly_price');
            $table->renameColumn('price_yearly', 'annual_price');
            $table->renameColumn('price_quarterly', 'quarterly_price');
            $table->renameColumn('price_semi_annual', 'semi_annual_price');
        });
    }
};
