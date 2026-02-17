<?php

namespace App\Services;

use App\Services\RouterosAPI;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    private $api;

    public function __construct()
    {
        $this->api = new RouterosAPI();
        // Enable debug if local
        $this->api->debug = config('app.debug', false);
    }

    public function connect($ip, $username, $password, $port = 8728)
    {
        $this->api->port = $port;
        // Attempt connection using the library
        if ($this->api->connect($ip, $username, $password)) {
            Log::info("Mikrotik Connected via RouterosAPI class to $ip:$port");
            return true;
        } else {
            Log::error("Mikrotik Connection Failed via RouterosAPI class to $ip:$port");
            return false;
        }
    }

    public function disconnect()
    {
        if ($this->api) {
            $this->api->disconnect();
        }
    }

    public function addPppSecret($user, $password, $service = 'any', $profile = 'default', $localAddress = null, $remoteAddress = null)
    {
        if (!$this->api->connected) return false;

        $params = [
            'name'       => $user,
            'password'   => $password,
            'service'    => $service,
            'profile'    => $profile,
        ];

        if ($localAddress) $params['local-address'] = $localAddress;
        if ($remoteAddress) $params['remote-address'] = $remoteAddress;

        // Use the library's comm method
        // comm accepts command string and array of params NOT prefixed with = if using array keys.
        // Wait, the library logic:
        // switch ($k[0]) { default: $el = "=$k=$v"; }
        // So we pass plain associative array: ['name' => 'foo'] -> =name=foo
        
        $response = $this->api->comm('/ppp/secret/add', $params);
        
        // !trap detection handled by library parseResponse?
        // Library returns array. If fail, usually contains keys like 'message' or 'trap'.
        // If success (add), usually returns empty array or !done (which library parses as valid).
        
        if (isset($response['!trap'])) {
            Log::error("Mikrotik Add Secret Failed: " . json_encode($response));
            return false;
        }
        
        // Sometimes parseResponse returns array of arrays if multiple checks.
        // For add command, if simple success, it returns empty array (or ID).
        // Let's assume success if no !trap
        return true;
    }
    
    public function removePppSecret($user)
    {
        if (!$this->api->connected) return false;
        
        // Find ID
        $print = $this->api->comm('/ppp/secret/print', [
            '?name' => $user
        ]);
        
        if (is_array($print) && count($print) > 0) {
            foreach ($print as $item) {
                if (isset($item['.id'])) {
                    $this->api->comm('/ppp/secret/remove', ['.id' => $item['.id']]);
                }
            }
            return true;
        }
        
        return false;
    }
}
