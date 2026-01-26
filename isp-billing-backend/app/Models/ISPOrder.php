<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ISPOrder extends Model
{
    use HasFactory;

    protected $table = 'isp_orders';

    protected $fillable = [
        'isp_id',
        'service_id',
        'subscription_package_id',
        'reference',
        'status',
        'payment_status',
        'price',
        'billing_cycle',
        'domain_type',
        'domain',
        'subdomain',
        'username',
        'password',
        'server_address',
        'ip_address',
        'l2tp_config',
        'sstp_config',
        'notes',
        'auto_renew',
        'start_date',
        'expired_date',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'auto_renew' => 'boolean',
        'start_date' => 'date',
        'expired_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function isp()
    {
        return $this->belongsTo(ISP::class);
    }

    public function service()
    {
        return $this->belongsTo(ISPService::class, 'service_id');
    }

    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'subscription_id')->where('billable_type', 'App\\Models\\ISP');
    }
}
