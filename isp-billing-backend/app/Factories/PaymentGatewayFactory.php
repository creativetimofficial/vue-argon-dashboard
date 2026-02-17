<?php

namespace App\Factories;

use App\Models\PaymentGateway;
use App\Services\Payment\MidtransService;
use App\Services\Payment\XenditService;
use App\Services\Payment\TripayService;
use App\Services\Payment\DuitkuService;
use Exception;

class PaymentGatewayFactory
{
    /**
     * Create a payment gateway instance.
     *
     * @param string|null $gatewayName Specific gateway name or null for default active
     * @return \App\Services\Payment\PaymentGatewayInterface
     * @throws Exception
     */
    public static function create($gatewayName = null)
    {
        if ($gatewayName) {
            $gateway = PaymentGateway::where('gateway_name', $gatewayName)->first();
        } else {
            // Find the active default gateway
            $gateway = PaymentGateway::where('is_active', true)->first();
        }

        if (!$gateway) {
            throw new Exception("No active payment gateway found.");
        }

        $config = [
            'api_key' => $gateway->api_key,
            'secret_key' => $gateway->secret_key,
            'merchant_id' => $gateway->merchant_id,
            'sandbox_mode' => $gateway->sandbox_mode,
        ];

        switch (strtolower($gateway->gateway_name)) {
            case 'midtrans':
                return new MidtransService($config);
            case 'xendit':
                return new XenditService($config);
            case 'tripay':
                return new TripayService($config);
            case 'duitku':
                return new DuitkuService($config);
            default:
                throw new Exception("Unsupported payment gateway: " . $gateway->gateway_name);
        }
    }
}
