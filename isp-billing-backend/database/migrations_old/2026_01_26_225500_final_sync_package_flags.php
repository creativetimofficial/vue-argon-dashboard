<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            // Comprehensive list of all feature flags used in seeder and UI
            $allFlags = [
                'feature_whitelabel',
                'feature_api_access',
                'feature_priority_support',
                'feature_analytics',
                'feature_multi_currency',
                'feature_automated_billing',
                'feature_free_subdomain',
                'feature_custom_domain',
                'feature_maps_interaktif',
                'feature_dynamic_forwarding',
                'feature_vpn_api',
                'feature_payment_gateways',
                'feature_custom_landing_page',
                'feature_realtime_monitoring',
                'feature_radius',
                'feature_acs',
                'feature_customer_portal',
                'feature_android_app',
            ];

            foreach ($allFlags as $flag) {
                if (!Schema::hasColumn('subscription_packages', $flag)) {
                    $table->boolean($flag)->default(false);
                }
            }
            
            // Ensure other metadata columns exist
            if (!Schema::hasColumn('subscription_packages', 'is_popular')) {
                $table->boolean('is_popular')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('subscription_packages', 'requires_manual_approval')) {
                $table->boolean('requires_manual_approval')->default(false)->after('is_popular');
            }
            if (!Schema::hasColumn('subscription_packages', 'active_days')) {
                $table->integer('active_days')->default(30)->after('sort_order');
            }
            if (!Schema::hasColumn('subscription_packages', 'trial_days')) {
                $table->integer('trial_days')->default(0)->after('active_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            // Usually we don't drop in a "fix" migration unless we're sure, 
            // but for completeness:
            // $table->dropColumn([...]);
        });
    }
};
