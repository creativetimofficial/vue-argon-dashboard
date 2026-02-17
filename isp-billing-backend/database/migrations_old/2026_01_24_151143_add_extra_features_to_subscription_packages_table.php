<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->boolean('feature_free_subdomain')->default(false);
            $table->boolean('feature_maps_interaktif')->default(false);
            $table->boolean('feature_dynamic_forwarding')->default(false);
            $table->boolean('feature_vpn_api')->default(false);
            $table->boolean('feature_payment_gateways')->default(false);
            $table->boolean('feature_custom_landing_page')->default(false);
            $table->boolean('feature_realtime_monitoring')->default(false);
            $table->boolean('feature_radius')->default(false);
            $table->boolean('feature_acs')->default(false);
            $table->boolean('feature_customer_portal')->default(false);
            $table->boolean('feature_android_app')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn([
                'feature_free_subdomain',
                'feature_maps_interaktif',
                'feature_dynamic_forwarding',
                'feature_vpn_api',
                'feature_payment_gateways',
                'feature_custom_landing_page',
                'feature_realtime_monitoring',
                'feature_radius',
                'feature_acs',
                'feature_customer_portal',
                'feature_android_app'
            ]);
        });
    }
};
