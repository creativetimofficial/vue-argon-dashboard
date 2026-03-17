<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISP extends Model
{
    use HasFactory;

    protected $table = 'isps';

    protected $fillable = [
        'owner_id',
        'company_name',
        'business_license',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'whatsapp',
        'email',
        'referral_code',
        'website',
        'logo',
        'subscription_package_id',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'billing_cycle',
        'approval_status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'is_active',
        'subdomain',
        'custom_domain',
        'favicon',
        'theme_color',
        'notification_settings',
        'wa_gateway_url',
        'wa_api_key',
    ];

    protected $casts = [
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'trial_end_date' => 'date',
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
        'notification_settings' => 'array',
    ];

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'isp_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function orders()
    {
        return $this->hasMany(ISPOrder::class, 'isp_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}