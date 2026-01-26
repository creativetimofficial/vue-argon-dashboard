<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISP extends Model
{
    use HasFactory;

    protected $table = 'isps';

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'logo',
        'website',
        'package_id',
        'subscription_package_id',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'trial_end_date',
        'tax_id',
        'business_license',
        'is_verified',
        'verified_at',
        'current_customers_count',
        'current_users_count',
        'current_locations_count',
        'approval_status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'referral_code',
        'is_active',
    ];

    protected $casts = [
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'trial_end_date' => 'date',
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function orders()
    {
        return $this->hasMany(ISPOrder::class);
    }
}