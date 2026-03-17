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
        // Ensure credentials AND a unique IP address exist before activation
        if (empty($this->username) || empty($this->ip_address)) {
            $this->generateCredentials();
        }

        $this->activateSubscription();
    }

    /**
     * Unified logic to activate a subscription order
     * Handles: Fair Extension, ISP Model Update, and Consolidation (Superseding)
     */
    public function activateSubscription()
    {
        // Guard: Don't activate if already active AND paid (prevent double counting days)
        // Benefit: Makes this method idempotent
        if ($this->status === 'active' && $this->payment_status === 'paid' && !empty($this->expired_date)) {
            \Illuminate\Support\Facades\Log::info("Order #{$this->id} already active and paid. Skipping redundant activation.");
            return $this;
        }

        $days = 30;
        if ($this->service_id) {
            $days = $this->service->trial_days ?: 30;
        } elseif ($this->subscription_package_id) {
            $days = $this->subscriptionPackage->active_days ?? 30;
        }

        $isp = $this->isp;
        
        // --- FAIR EXTENSION LOGIC ---
        // If ISP already has an active subscription, extend from the current end date
        if ($isp && $isp->subscription_end_date && \Carbon\Carbon::parse($isp->subscription_end_date)->isFuture()) {
            $newStart = $isp->subscription_start_date ?? now();
            $newEnd = \Carbon\Carbon::parse($isp->subscription_end_date)->addDays($days);
        } else {
            // Otherwise start from now
            $newStart = now();
            $newEnd = now()->addDays($days);
        }

        // Update Order
        $this->update([
            'status' => 'active',
            'payment_status' => 'paid',
            'domain_active' => true,
            'start_date' => $newStart,
            'expired_date' => $newEnd,
            'approved_at' => $this->approved_at ?: now(),
        ]);

        // Update ISP Model (If package-based)
        if ($this->subscription_package_id) {
            $isp->update([
                'subscription_package_id' => $this->subscription_package_id,
                'subscription_status' => 'active',
                'subscription_start_date' => $newStart,
                'subscription_end_date' => $newEnd,
                'is_active' => true,
            ]);
            
            // Also ensure all ISP users are active
            $isp->users()->update(['is_active' => true]);
        }

        // --- CONSOLIDATION LOGIC ---
        // Mark all other active/approved/trial orders for the SAME ISP as superseded
        static::where('isp_id', $this->isp_id)
            ->where('id', '!=', $this->id)
            ->whereIn('status', ['active', 'approved', 'trial'])
            ->update(['status' => 'superseded']);
        
        return $this;
    }

    /**
     * Generate VPN credentials and configs
     */
    public function generateCredentials()
    {
        $username = $this->username ?: date('YmdHis') . rand(100, 999);
        $password = $this->password ?: \Illuminate\Support\Str::random(10);
        
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
     * Generate a unique private IP address from the server's VPN segment
     */
    public function generateUniqueIp()
    {
        if ($this->ip_address) return $this->ip_address;

        $server = $this->server;
        if (!$server) {
            $server = Server::where('is_active', true)->inRandomOrder()->first();
            if ($server) $this->server_id = $server->id;
        }

        if (!$server) return '10.254.254.254';

        $localAddress = $server->vpn_local_address ?? '10.10.10.1';
        $ipParts = explode('.', $localAddress);
        if (count($ipParts) !== 4) return '10.10.10.254';

        $baseOctet1 = $ipParts[0];
        $baseOctet2 = $ipParts[1];
        $baseOctet3 = (int)$ipParts[2];

        // Gather used IPs across DB
        $usedIps = static::where('server_id', $server->id)
            ->whereNotNull('ip_address')
            ->pluck('ip_address')
            ->toArray();

        $capacity = $server->capacity ?? 1000;
        $checkedCount = 0;

        for ($subnetOffset = 0; $subnetOffset < 20; $subnetOffset++) {
            $currentSubnet = $baseOctet3 + $subnetOffset;
            if ($currentSubnet > 254) break;

            for ($h = 1; $h <= 254; $h++) {
                $candidate = "{$baseOctet1}.{$baseOctet2}.{$currentSubnet}.{$h}";
                
                if ($candidate === $localAddress) continue;

                if (!in_array($candidate, $usedIps)) {
                    return $candidate;
                }

                $checkedCount++;
                if ($checkedCount >= $capacity + 500) break;
            }
        }
        
        return '10.254.254.254';
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
