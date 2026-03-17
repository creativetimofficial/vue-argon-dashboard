<?php

namespace App\Observers;

use App\Models\ISPOrder;
use App\Models\ISP;
use App\Services\MikrotikService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ISPOrderObserver
{
    /**
     * Handle the ISPOrder "created" event.
     */
    public function created(ISPOrder $order): void
    {
        // Check if order is immediately active (e.g. trial)
        if ($order->status === 'active') {
            $this->assignSubdomainToISP($order);
            $this->provisionVPN($order);
        }
    }

    /**
     * Handle the ISPOrder "updated" event.
     * Auto-assign subdomain when order is approved
     */
    public function updated(ISPOrder $order): void
    {
        // 1. Trigger if status changed to 'active'
        if ($order->isDirty('status') && $order->status === 'active') {
            $this->assignSubdomainToISP($order);
            $this->provisionVPN($order);
        }
        
        // 2. Trigger if credentials were just assigned to an already active order
        elseif ($order->status === 'active' && ($order->isDirty('username') || $order->isDirty('ip_address')) && !empty($order->username)) {
            $this->provisionVPN($order);
        }

        // 3. Trigger if status changed to inactive (expired, suspended, cancelled)
        elseif ($order->isDirty('status') && in_array($order->status, ['expired', 'suspended', 'cancelled'])) {
            $this->deprovisionVPN($order);
        }
    }

    /**
     * Handle the ISPOrder "deleted" event.
     */
    public function deleted(ISPOrder $order): void
    {
        $this->deprovisionVPN($order);
    }

    /**
     * Provision VPN account on Mikrotik server
     */
    private function provisionVPN(ISPOrder $order): void
    {
        if (empty($order->username) || empty($order->password)) {
            Log::warning("Skipping VPN provisioning for Order #{$order->id}: Missing credentials");
            return;
        }

        $server = $order->server;
        if (!$server) {
            Log::error("VPN Provisioning Failed for Order #{$order->id}: No server assigned");
            return;
        }

        try {
            $mikrotik = new MikrotikService();
            if ($mikrotik->connect($server->ip_address, $server->username, $server->password, $server->api_port ?? 8728)) {
                // Determine Local Address
                $localAddress = $server->vpn_local_address ?? '10.10.10.1'; // Use configured or default
                
                // Add PPP Secret
                $success = $mikrotik->addPppSecret(
                    $order->username,
                    $order->password,
                    'any', // Service
                    'default', // Profile
                    $localAddress, // Local address
                    $order->ip_address // Remote address
                );

                if ($success) {
                    Log::info("VPN Provisioned successfully on server '{$server->name}' for Order #{$order->id}");
                } else {
                    Log::error("Failed to add PPP Secret on server '{$server->name}' for Order #{$order->id}");
                }

                $mikrotik->disconnect();
            } else {
                Log::error("Could not connect to Mikrotik server '{$server->name}' ({$server->ip_address}) for Order #{$order->id}");
            }
        } catch (\Exception $e) {
            Log::error("VPN Provisioning Error for Order #{$order->id}: " . $e->getMessage());
        }
    }

    /**
     * Remove VPN account from Mikrotik server
     */
    private function deprovisionVPN(ISPOrder $order): void
    {
        if (empty($order->username)) {
            return;
        }

        $server = $order->server;
        if (!$server) {
            return;
        }

        try {
            $mikrotik = new MikrotikService();
            if ($mikrotik->connect($server->ip_address, $server->username, $server->password, $server->api_port ?? 8728)) {
                if ($mikrotik->removePppSecret($order->username)) {
                    Log::info("VPN Deprovisioned (removed) from server '{$server->name}' for Order #{$order->id}");
                    if ($server->current_users > 0) {
                        $server->decrement('current_users');
                    }
                }
                $mikrotik->disconnect();
            }
        } catch (\Exception $e) {
            Log::error("VPN Deprovisioning Error for Order #{$order->id}: " . $e->getMessage());
        }
    }

    /**
     * Assign subdomain and custom domain to ISP automatically
     */
    private function assignSubdomainToISP(ISPOrder $order): void
    {
        $isp = $order->isp;
        
        if (!$isp) {
            return;
        }

        // Prepare update data
        $updateData = ['is_active' => true];

        // Handle subdomain (if not already set)
        if (!$isp->subdomain) {
            // Use subdomain from order, or generate from company name
            $subdomain = $order->subdomain ?? $this->generateSubdomain($isp->company_name);
            
            // Ensure uniqueness
            $subdomain = $this->ensureUniqueSubdomain($subdomain);
            
            $updateData['subdomain'] = $subdomain;
        }

        // Handle custom domain (if provided in order)
        if ($order->domain_type === 'custom' && $order->domain) {
            // Check if custom domain is unique
            $existingDomain = ISP::where('custom_domain', $order->domain)
                ->where('id', '!=', $isp->id)
                ->exists();
            
            if (!$existingDomain) {
                $updateData['custom_domain'] = $order->domain;
                $updateData['custom_domain_verified'] = false; // Needs verification
            } else {
                \Log::warning("Custom domain '{$order->domain}' already exists, skipping assignment for ISP #{$isp->id}");
            }
        }

        // Update ISP
        $isp->update($updateData);

        // Log the assignment
        $logMessage = "ISP #{$isp->id} ({$isp->company_name}) updated:";
        if (isset($updateData['subdomain'])) {
            $logMessage .= " subdomain='{$updateData['subdomain']}'";
        }
        if (isset($updateData['custom_domain'])) {
            $logMessage .= " custom_domain='{$updateData['custom_domain']}'";
        }
        \Log::info($logMessage);
    }

    /**
     * Generate subdomain from company name
     */
    private function generateSubdomain(string $companyName): string
    {
        // Convert to lowercase, remove special characters, replace spaces with dash
        $subdomain = Str::slug($companyName);
        
        // Limit to 50 characters (database constraint)
        $subdomain = Str::limit($subdomain, 50, '');
        
        // Remove trailing dash if any
        $subdomain = rtrim($subdomain, '-');
        
        return $subdomain;
    }

    /**
     * Ensure subdomain is unique by appending number if needed
     */
    private function ensureUniqueSubdomain(string $subdomain): string
    {
        $originalSubdomain = $subdomain;
        $counter = 1;

        while (ISP::where('subdomain', $subdomain)->exists()) {
            $subdomain = $originalSubdomain . '-' . $counter;
            $counter++;
        }

        return $subdomain;
    }
}
