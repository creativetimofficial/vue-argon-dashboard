<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            // Feature Flags - Core Features
            $table->boolean('feature_free_subdomain')->default(true)->after('features');
            $table->boolean('feature_custom_domain')->default(false)->after('feature_free_subdomain');
            $table->boolean('feature_maps_interaktif')->default(false)->after('feature_custom_domain');
            $table->boolean('feature_dynamic_forwarding')->default(false)->after('feature_maps_interaktif');
            $table->boolean('feature_realtime_monitoring')->default(false)->after('feature_dynamic_forwarding');
            $table->boolean('feature_radius')->default(false)->after('feature_realtime_monitoring');
            $table->boolean('feature_acs')->default(false)->after('feature_radius');
            $table->boolean('feature_android_app')->default(false)->after('feature_acs');
            
            // Feature Flags - Important Features
            $table->boolean('feature_payment_gateways')->default(false)->after('feature_android_app');
            $table->boolean('feature_api_access')->default(false)->after('feature_payment_gateways');
            $table->boolean('feature_custom_landing_page')->default(false)->after('feature_api_access');
            $table->boolean('feature_customer_portal')->default(false)->after('feature_custom_landing_page');
            $table->boolean('feature_whatsapp_gateway')->default(false)->after('feature_customer_portal');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn([
                'feature_free_subdomain',
                'feature_custom_domain',
                'feature_maps_interaktif',
                'feature_dynamic_forwarding',
                'feature_realtime_monitoring',
                'feature_radius',
                'feature_acs',
                'feature_android_app',
                'feature_payment_gateways',
                'feature_api_access',
                'feature_custom_landing_page',
                'feature_customer_portal',
                'feature_whatsapp_gateway',
            ]);
        });
    }
};
