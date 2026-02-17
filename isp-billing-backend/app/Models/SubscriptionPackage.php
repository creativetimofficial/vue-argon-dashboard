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
        'price',
        'active_days',
        'discount_percent',
        'trial_days',
        'max_customers',
        'max_users',
        'max_locations',
        'max_invoices',
        'email_support',
        'custom_branding',
        'multi_user_access',
        'features',
        'is_active',
        'is_popular',
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
        'feature_payment_gateways',
        'feature_custom_landing_page',
        'feature_realtime_monitoring',
        'feature_radius',
        'feature_acs',
        'feature_customer_portal',
        'feature_android_app',
        'feature_whatsapp_gateway',
        'feature_whitelabel',
        'feature_priority_support',
        'feature_analytics',
        'feature_multi_currency', 
        'feature_automated_billing',
        'requires_manual_approval',
        'active_days',
        'trial_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'integer',
        'max_customers' => 'integer',
        'max_users' => 'integer',
        'max_locations' => 'integer',
        'max_invoices' => 'integer',
        'email_support' => 'boolean',
        'whatsapp_support' => 'boolean',
        'custom_branding' => 'boolean',
        'api_access' => 'boolean',
        'multi_user_access' => 'boolean',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
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
        'feature_payment_gateways' => 'boolean',
        'feature_custom_landing_page' => 'boolean',
        'feature_realtime_monitoring' => 'boolean',
        'feature_radius' => 'boolean',
        'feature_acs' => 'boolean',
        'feature_customer_portal' => 'boolean',
        'feature_android_app' => 'boolean',
        'feature_whatsapp_gateway' => 'boolean',
        'discount_percent' => 'integer',
        'trial_days' => 'integer',
        'requires_manual_approval' => 'boolean',
        'active_days' => 'integer',
    ];

    public function isps()
    {
        return $this->hasMany(ISP::class);
    }
}