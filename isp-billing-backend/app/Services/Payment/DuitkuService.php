<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class DuitkuService implements PaymentGatewayInterface
{
    private $config;
    private $baseUrl;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->baseUrl = $config['sandbox_mode'] 
            ? 'https://sandbox.duitku.com/webapi/api/merchant' 
            : 'https://passport.duitku.com/webapi/api/merchant';
    }

    public function createInvoice($externalId, $amount, $payerName, $payerEmail, $description, $items = [])
    {
        $apiKey = $this->config['api_key']; 
        $merchantCode = $this->config['merchant_id'];
        $amountInt = (int) $amount;
        
        // Signature: MD5(merchantCode + merchantOrderId + paymentAmount + apiKey)
        $signature = md5($merchantCode . $externalId . $amountInt . $apiKey);

        $payload = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $amountInt,
            'merchantOrderId' => $externalId,
            'productDetails' => $description,
            'email' => $payerEmail,
            'customerVaName' => $payerName, // Name for VA
            'callbackUrl' => config('app.frontend_url') . '/payment-callback', // Frontend or Backend? Usually Backend for notification, Frontend for return.
            'returnUrl' => config('app.frontend_url') . '/payment-callback?order_id=' . $externalId, // Redirect here after finish
            'signature' => $signature,
            'expiryPeriod' => 1440, // 24 hours
            // 'paymentMethod' => '', // Empty = Select on Duitku page
        ];

        // Duitku Create Invoice usually via POST to /v2/inquiry or similar
        // Docs say: POST to /v2/inquiry returns the paymentUrl
        
        $response = Http::post("{$this->baseUrl}/v2/inquiry", $payload);

        if ($response->failed()) {
            throw new Exception("Duitku Error: " . $response->body());
        }
        
        $data = $response->json();
        
        if (isset($data['statusCode']) && $data['statusCode'] != '00') {
            throw new Exception("Duitku Failed: " . ($data['statusMessage'] ?? 'Unknown'));
        }

        return [
            'status' => 'success',
            'payment_url' => $data['paymentUrl'],
            'payment_token' => $data['reference'], // Duitku Reference
            'raw_response' => $data
        ];
    }

    public function handleCallback(Request $request)
    {
        // Duitku Callback
        // Signature: MD5(merchantCode + amount + merchantOrderId + apiKey)
        $apiKey = $this->config['api_key'];
        $merchantCode = $this->config['merchant_id'];
        
        $amount = $request->input('amount');
        $merchantOrderId = $request->input('merchantOrderId');
        $signature = $request->input('signature');
        
        $calcSignature = md5($merchantCode . $amount . $merchantOrderId . $apiKey);
        
        // if ($signature !== $calcSignature) { ... }

        $resultCode = $request->input('resultCode');
        $status = 'pending';
        
        if ($resultCode == '00') {
            $status = 'paid';
        } else if ($resultCode == '01') {
            $status = 'failed';
        }

        return [
            'external_id' => $merchantOrderId,
            'status' => $status,
            'payment_method' => $request->input('paymentMethod') ?? 'Duitku',
            'amount' => $amount,
            'raw_response' => json_encode($request->all()),
        ];
    }
}
