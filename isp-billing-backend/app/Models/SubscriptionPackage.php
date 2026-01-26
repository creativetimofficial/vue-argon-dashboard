<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'discount_yearly_percent',
        'max_customers',
        'max_users',
        'max_locations',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
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

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'discount_yearly_percent' => 'integer',
        'max_customers' => 'integer',
        'max_users' => 'integer',
        'max_locations' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'feature_whitelabel' => 'boolean',
        'feature_api_access' => 'boolean',
        'feature_priority_support' => 'boolean',
        'feature_analytics' => 'boolean',
        'feature_multi_currency' => 'boolean',
        'feature_automated_billing' => 'boolean',
        'feature_free_subdomain' => 'boolean',
        'feature_custom_domain' => 'boolean',
        'feature_maps_interaktif' => 'boolean',
        'feature_dynamic_forwarding' => 'boolean',
        'feature_vpn_api' => 'boolean',
        'feature_payment_gateways' => 'boolean',
        'feature_custom_landing_page' => 'boolean',
        'feature_realtime_monitoring' => 'boolean',
        'feature_radius' => 'boolean',
        'feature_acs' => 'boolean',
        'feature_customer_portal' => 'boolean',
        'feature_android_app' => 'boolean',
    ];

    public function isps()
    {
        return $this->hasMany(ISP::class);
    }
}