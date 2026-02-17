<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            // Price tiers if missing (based on early schema versions)
            if (!Schema::hasColumn('subscription_packages', 'price_quarterly')) {
                $table->decimal('price_quarterly', 12, 2)->nullable()->after('price_monthly');
            }
            if (!Schema::hasColumn('subscription_packages', 'price_semi_annual')) {
                $table->decimal('price_semi_annual', 12, 2)->nullable()->after('price_quarterly');
            }
            if (!Schema::hasColumn('subscription_packages', 'price_yearly')) {
                $table->decimal('price_yearly', 12, 2)->nullable()->after('price_semi_annual');
            }

            // Duration fields
            if (!Schema::hasColumn('subscription_packages', 'active_days')) {
                $table->integer('active_days')->default(30)->after('sort_order');
            }
            if (!Schema::hasColumn('subscription_packages', 'trial_days')) {
                $table->integer('trial_days')->default(0)->after('active_days');
            }

            // Marketing flags
            if (!Schema::hasColumn('subscription_packages', 'is_popular')) {
                $table->boolean('is_popular')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('subscription_packages', 'requires_manual_approval')) {
                $table->boolean('requires_manual_approval')->default(false)->after('is_popular');
            }

            // Technical feature flags (ensuring all from seeder/UI exist)
            $newFlags = [
                'feature_whitelabel',
                'feature_api_access',
                'feature_priority_support',
                'feature_analytics',
                'feature_multi_currency',
                'feature_automated_billing',
            ];

            foreach ($newFlags as $flag) {
                if (!Schema::hasColumn('subscription_packages', $flag)) {
                    $table->boolean($flag)->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn([
                'price_quarterly',
                'price_semi_annual',
                'price_yearly',
                'active_days',
                'trial_days',
                'is_popular',
                'requires_manual_approval',
                'feature_whitelabel',
                'feature_api_access',
                'feature_priority_support',
                'feature_analytics',
                'feature_multi_currency',
                'feature_automated_billing',
            ]);
        });
    }
};
