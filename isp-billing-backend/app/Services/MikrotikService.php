<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    protected $api;

    public function __construct()
    {
        $this->api = new \App\Services\RouterosAPI();
    }

    /**
     * Connect to Mikrotik RouterOS.
     * 
     * @param mixed $ip_or_ispId The ID of the ISP, or the IP address string if $user is provided.
     * @param string|null $user The Mikrotik username.
     * @param string|null $pass The Mikrotik password.
     * @param int $port The api port.
     * @return bool True if connection is successful.
     */
    public function connect($ip_or_ispId, $user = null, $pass = null, $port = 8728)
    {
        if ($user !== null) {
            $this->api->port = $port;
            return $this->api->connect($ip_or_ispId, $user, $pass);
        }

        $ispId = $ip_or_ispId;
        // TODO: Retrieve the primary Mikrotik credentials for the given $ispId.
        // Initialize connection.
        Log::info("MikrotikService: Attempting connection for ISP ID {$ispId}");
        
        // Mocking a successful connection for now.
        return true;
    }

    /**
     * Disconnect from Mikrotik RouterOS.
     */
    public function disconnect()
    {
        $this->api->disconnect();
    }

    /**
     * Add a PPP Secret to the Mikrotik.
     *
     * @param string $username
     * @param string $password
     * @param string $service
     * @param string $profile
     * @param string|null $localAddress
     * @param string|null $remoteAddress
     * @return bool
     */
    public function addPppSecret($username, $password, $service = 'any', $profile = 'default', $localAddress = null, $remoteAddress = null)
    {
        $params = [
            'name' => $username,
            'password' => $password,
            'service' => $service,
            'profile' => $profile,
        ];

        if ($localAddress) {
            $params['local-address'] = $localAddress;
        }

        if ($remoteAddress) {
            $params['remote-address'] = $remoteAddress;
        }

        $response = $this->api->comm('/ppp/secret/add', $params);

        if (isset($response['!trap'])) {
            Log::error("MikrotikService addPppSecret failed: " . json_encode($response));
            return false;
        }

        return true;
    }

    /**
     * Check if the router is online and retrieve basic resource stats (CPU, RAM).
     * 
     * @param int $ispId
     * @return array
     */
    public function getRouterHealth($ispId)
    {
        if (!$this->connect($ispId)) {
            return ['status' => 'offline', 'cpu' => 0, 'ram_free' => 0];
        }

        // Mock data. Will be replaced by actual RouterOS API queries:
        // /system/resource/print
        return [
            'status' => 'online',
            'cpu' => rand(5, 30) . '%',
            'ram_free' => '1.2 GB',
            'uptime' => '14d 2h 45m',
            'board_name' => 'RB4011iGS+RM',
        ];
    }

    /**
     * Automate customer suspension via PPPoE/Hotspot API.
     * 
     * @param Customer $customer
     * @return bool
     */
    public function suspendCustomer(Customer $customer)
    {
        if (!$this->connect($customer->isp_id)) {
            Log::error("Failed to suspend customer {$customer->id} due to Router API failure.");
            return false;
        }

        // TODO: Move PPPoE Profile to 'ISOLIR' or disable the secret.
        // Client: /ppp/secret/set numbers=[find name=$username] profile=ISOLIR
        Log::info("MikrotikService: Suspended customer {$customer->id} (PPPoE: {$customer->mikrotik_username})");
        
        $customer->update(['status' => 'suspended']);
        
        return true;
    }

    /**
     * Automate customer reactivation.
     * 
     * @param Customer $customer
     * @return bool
     */
    public function activateCustomer(Customer $customer)
    {
        if (!$this->connect($customer->isp_id)) {
            Log::error("Failed to activate customer {$customer->id} due to Router API failure.");
            return false;
        }

        // TODO: Restore PPPoE profile to the active high-speed tier based on ServicePlan.
        Log::info("MikrotikService: Activated customer {$customer->id} (PPPoE: {$customer->mikrotik_username})");
        
        $customer->update(['status' => 'active']);
        
        return true;
    }

    /**
     * Retrieve live bandwidth usage for a specific user.
     * 
     * @param Customer $customer
     * @return array
     */
    public function getLiveTraffic(Customer $customer)
    {
        // Requires tracking the 'active' connections via RouterOS API.
        // /interface/monitor-traffic interface="<pppoe-username>" once
        return [
            'rx_byte' => rand(1000, 5000000), // Random simulated traffic
            'tx_byte' => rand(1000, 2000000),
        ];
    }
}
