<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'isp_id',
        'customer_number',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'id_number',
        'id_type',
        'package_id',
        'subscription_id',
        'status',
        'installation_address',
        'billing_address',
        'mikrotik_username',
        'mikrotik_password',
        'ip_address',
        'mac_address',
    ];

    protected $casts = [
        'installation_address' => 'array',
        'billing_address' => 'array',
    ];

    /**
     * Get the user record associated with the customer.
     */
    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Get the ISP that owns the customer.
     */
    public function isp()
    {
        return $this->belongsTo(ISP::class);
    }

    /**
     * Get the package subscribed by customer.
     */
    public function package()
    {
        return $this->belongsTo(CustomerPackage::class, 'package_id');
    }

    /**
     * Get the active subscription.
     */
    public function subscription()
    {
        return $this->belongsTo(CustomerSubscription::class, 'subscription_id');
    }

    /**
     * Get all subscriptions history.
     */
    public function subscriptions()
    {
        return $this->hasMany(CustomerSubscription::class);
    }

    /**
     * Get invoices for this customer.
     */
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'billable');
    }

    /**
     * Get payments made by customer.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get installation requests.
     */
    public function installationRequests()
    {
        return $this->hasMany(InstallationRequest::class);
    }

    /**
     * Get repair tickets.
     */
    public function repairTickets()
    {
        return $this->hasMany(RepairTicket::class);
    }

    /**
     * Get complaints.
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    /**
     * Check if customer is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if customer has overdue invoices.
     */
    public function hasOverdueInvoices(): bool
    {
        return $this->invoices()
            ->where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->exists();
    }

    /**
     * Scope for active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for specific ISP.
     */
    public function scopeForISP($query, $ispId)
    {
        return $query->where('isp_id', $ispId);
    }
}
