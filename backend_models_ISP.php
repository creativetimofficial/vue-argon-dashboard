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
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'tax_id',
        'business_license',
        'is_verified',
        'is_active',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
    ];

    /**
     * Get the package this ISP is subscribed to.
     */
    public function package()
    {
        return $this->belongsTo(SuperAdminPackage::class, 'package_id');
    }

    /**
     * Get all admins for this ISP.
     */
    public function admins()
    {
        return $this->hasMany(ISPAdmin::class);
    }

    /**
     * Get the primary admin for this ISP.
     */
    public function primaryAdmin()
    {
        return $this->hasOne(ISPAdmin::class)->where('is_primary', true);
    }

    /**
     * Get all customers for this ISP.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get all technicians for this ISP.
     */
    public function technicians()
    {
        return $this->hasMany(Technician::class);
    }

    /**
     * Get all customer packages for this ISP.
     */
    public function customerPackages()
    {
        return $this->hasMany(CustomerPackage::class);
    }

    /**
     * Get all subscriptions for this ISP.
     */
    public function subscriptions()
    {
        return $this->hasMany(ISPSubscription::class);
    }

    /**
     * Get active subscription.
     */
    public function activeSubscription()
    {
        return $this->hasOne(ISPSubscription::class)
            ->where('status', 'active')
            ->latest();
    }

    /**
     * Get all mikrotik routers for this ISP.
     */
    public function mikrotikRouters()
    {
        return $this->hasMany(MikrotikRouter::class);
    }

    /**
     * Get all invoices for this ISP.
     */
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'billable');
    }

    /**
     * Get all payment gateways for this ISP.
     */
    public function paymentGateways()
    {
        return $this->hasMany(PaymentGateway::class);
    }

    /**
     * Get settings for this ISP.
     */
    public function settings()
    {
        return $this->hasMany(ISPSetting::class);
    }

    /**
     * Scope to filter active ISPs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter verified ISPs.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter by subscription status.
     */
    public function scopeSubscriptionStatus($query, $status)
    {
        return $query->where('subscription_status', $status);
    }

    /**
     * Check if ISP subscription is active.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active'
            && $this->subscription_end_date
            && $this->subscription_end_date->isFuture();
    }

    /**
     * Check if ISP is within customer limit.
     */
    public function isWithinCustomerLimit(): bool
    {
        if (!$this->package) {
            return false;
        }

        $maxCustomers = $this->package->max_customers;

        if ($maxCustomers === -1) {
            return true; // Unlimited
        }

        return $this->customers()->count() < $maxCustomers;
    }

    /**
     * Get customer usage percentage.
     */
    public function getCustomerUsagePercentage(): float
    {
        if (!$this->package || $this->package->max_customers === -1) {
            return 0;
        }

        $current = $this->customers()->count();
        $max = $this->package->max_customers;

        return ($current / $max) * 100;
    }

    /**
     * Get total revenue.
     */
    public function getTotalRevenue()
    {
        return $this->invoices()
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    /**
     * Get active customers count.
     */
    public function getActiveCustomersCount(): int
    {
        return $this->customers()
            ->where('service_status', 'active')
            ->count();
    }
}
