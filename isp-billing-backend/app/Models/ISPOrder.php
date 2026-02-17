<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ISPOrder extends Model
{
    use HasFactory, SoftDeletes;

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
        'service_name',
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
        'payment_expired_at',
        'payment_gateway',
        'payment_reference',
        'domain_active',
        'retry_count',
        'last_retry_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'auto_renew' => 'boolean',
        'domain_active' => 'boolean',
        'retry_count' => 'integer',
        'start_date' => 'date',
        'expired_date' => 'date',
        'approved_at' => 'datetime',
        'payment_expired_at' => 'datetime',
        'last_retry_at' => 'datetime',
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

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'subscription_id')->where('billable_type', 'App\\Models\\ISP');
    }

    // Scopes
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending_payment');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function isDomainAccessible()
    {
        return $this->status === 'active' && $this->domain_active === true;
    }

    // Methods
    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired',
            'payment_status' => 'expired',
            'domain_active' => false,
        ]);
    }

    public function activateDomain()
    {
        // Ensure credentials exist before activation
        if (empty($this->username)) {
            $this->generateCredentials();
        }

        $this->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'domain_active' => true,
            'start_date' => now(),
        ]);
    }

    /**
     * Generate VPN credentials and configs
     */
    public function generateCredentials()
    {
        $username = date('YmdHis') . rand(100, 999);
        $password = \Illuminate\Support\Str::random(10);
        
        // Use assigned server or find an active one
        $serverAddress = 'server-not-found';
        if ($this->server_id) {
            $server = Server::find($this->server_id);
            if ($server) {
                $serverAddress = $server->domain ?? $server->ip_address;
            }
        } else {
            $server = Server::where('is_active', true)->inRandomOrder()->first();
            if ($server) {
                $this->server_id = $server->id;
                $serverAddress = $server->domain ?? $server->ip_address;
            }
        }

        // Generate Unique IP
        $ip = $this->generateUniqueIp();
        
        $localAddr = '10.10.10.1'; // Default Fallback
        if ($this->server && $this->server->vpn_local_address) {
            $localAddr = $this->server->vpn_local_address;
        }

        $l2tpConfig = "/interface l2tp-client add name={$username} user={$username} password={$password} connect-to={$serverAddress} profile=default local-address={$localAddr} remote-address={$ip} disabled=no";
        $sstpConfig = "/interface sstp-client add name={$username} user={$username} password={$password} connect-to={$serverAddress} profile=default local-address={$localAddr} remote-address={$ip} disabled=no";

        $this->update([
            'username' => $username,
            'password' => $password,
            'server_address' => $serverAddress,
            'ip_address' => $ip,
            'l2tp_config' => $l2tpConfig,
            'sstp_config' => $sstpConfig,
        ]);
        
        return $this;
    }

    /**
     * Generate a unique private IP address
     */
    public function generateUniqueIp()
    {
        $maxAttempts = 100;
        $subnets = [50, 51, 52, 53];
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            $subnet = $subnets[array_rand($subnets)];
            $ip = "10.{$subnet}." . rand(2, 254) . '.' . rand(2, 254);
            
            // Allow reusing IPs from expired/failed/cancelled orders to prevent exhaustion
            // We only block IPs currently assigned to "living" orders
            $exists = static::where('ip_address', $ip)
                ->whereIn('status', ['active', 'trial', 'pending', 'pending_payment'])
                ->exists();
                
            if (!$exists) {
                return $ip;
            }
        }
        
        // Final fallback if many collisions (unlikely with 250k pool)
        $subnet = $subnets[array_rand($subnets)];
        return "10.{$subnet}." . rand(2, 254) . '.' . rand(2, 254);
    }

    public function canRetryPayment()
    {
        return in_array($this->status, ['expired', 'failed']) && $this->payment_status !== 'paid';
    }

    public function incrementRetryCount()
    {
        $this->increment('retry_count');
        $this->update(['last_retry_at' => now()]);
    }
}
