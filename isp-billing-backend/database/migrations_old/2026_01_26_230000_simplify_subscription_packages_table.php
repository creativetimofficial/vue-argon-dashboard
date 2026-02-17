<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            // Rename price_monthly to price
            if (Schema::hasColumn('subscription_packages', 'price_monthly')) {
                $table->renameColumn('price_monthly', 'price');
            } else if (Schema::hasColumn('subscription_packages', 'monthly_price')) {
                $table->renameColumn('monthly_price', 'price');
            }

            // Rename discount_yearly_percent to discount_percent
            if (Schema::hasColumn('subscription_packages', 'discount_yearly_percent')) {
                $table->renameColumn('discount_yearly_percent', 'discount_percent');
            }

            // Drop complex price columns
            $colsToDrop = [];
            if (Schema::hasColumn('subscription_packages', 'price_quarterly')) $colsToDrop[] = 'price_quarterly';
            if (Schema::hasColumn('subscription_packages', 'price_semi_annual')) $colsToDrop[] = 'price_semi_annual';
            if (Schema::hasColumn('subscription_packages', 'price_yearly')) $colsToDrop[] = 'price_yearly';
            if (Schema::hasColumn('subscription_packages', 'quarterly_price')) $colsToDrop[] = 'quarterly_price';
            if (Schema::hasColumn('subscription_packages', 'semi_annual_price')) $colsToDrop[] = 'semi_annual_price';
            if (Schema::hasColumn('subscription_packages', 'annual_price')) $colsToDrop[] = 'annual_price';
            
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }

            // Ensure is_featured is renamed to is_popular if still exists
            if (Schema::hasColumn('subscription_packages', 'is_featured')) {
                $table->renameColumn('is_featured', 'is_popular');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            // Usually we don't reverse simplification migrations but for safety:
            if (Schema::hasColumn('subscription_packages', 'price')) {
                $table->renameColumn('price', 'price_monthly');
            }
            if (Schema::hasColumn('subscription_packages', 'discount_percent')) {
                $table->renameColumn('discount_percent', 'discount_yearly_percent');
            }
            if (Schema::hasColumn('subscription_packages', 'is_popular')) {
                $table->renameColumn('is_popular', 'is_featured');
            }
        });
    }
};
