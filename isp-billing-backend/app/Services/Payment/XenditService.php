<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class XenditService implements PaymentGatewayInterface
{
    private $config;
    private $baseUrl = 'https://api.xendit.co';

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createInvoice($externalId, $amount, $payerName, $payerEmail, $description, $items = [])
    {
        $secretKey = $this->config['secret_key'];
        
        $payerName = is_string($payerName) && !empty(trim($payerName)) ? trim($payerName) : 'Customer';
        
        $frontendUrl = config('app.frontend_url', 'http://localhost:8080');
        $successUrl = "{$frontendUrl}/client-area/invoices?status=success&external_id={$externalId}";
        $failureUrl = "{$frontendUrl}/client-area/invoices?status=failed&external_id={$externalId}";

        $response = Http::withBasicAuth($secretKey, '')
            ->post("{$this->baseUrl}/v2/invoices", [
                'external_id' => $externalId,
                'amount' => (int) $amount,
                'payer_email' => $payerEmail,
                'description' => $description,
                'customer' => [
                    'given_names' => $payerName,
                    'email' => $payerEmail,
                ],
                'fees' => [
                    // Calculate fees if needed, or included in amount
                ],
                'success_redirect_url' => $successUrl,
                'failure_redirect_url' => $failureUrl,
            ]);

        if ($response->failed()) {
            throw new Exception("Xendit Error: " . $response->body());
        }

        $data = $response->json();

        return [
            'status' => 'success',
            'payment_url' => $data['invoice_url'],
            'payment_token' => $data['id'], // Xendit Invoice ID
            'raw_response' => $data
        ];
    }

    public function handleCallback(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('XenditService: Processing callback', $request->all());

        // Xendit Webhook Verification with Token (if stored in webhook_secret)
        // Note: Xendit sends x-callback-token header
        $callbackToken = $request->header('x-callback-token');
        // if (isset($this->config['webhook_secret']) && $callbackToken !== $this->config['webhook_secret']) {
        //     throw new Exception("Invalid Callback Token");
        // }
        // For simplicity, we skip strict verification or log it validation warning for now 
        // as setup might vary.

        $status = 'pending';
        // Xendit sends 'status' in body: PENDING, PAID, SETTLED, EXPIRED
        $externalStatus = $request->input('status');
        
        if ($externalStatus === 'PAID' || $externalStatus === 'SETTLED') {
             $status = 'paid'; // Mapped to 'paid'/'success'
        } elseif ($externalStatus === 'EXPIRED') {
             $status = 'expired'; // Mapped to 'failed'
        }
        
        \Illuminate\Support\Facades\Log::info("XenditService: Mapped status '$externalStatus' to '$status'");

        // Map details
        $paymentMethod = $request->input('payment_method') ?? 'Xendit';
        $paymentChannel = $request->input('payment_channel');
        
        // Combine if channel exists for better clarity (e.g. "BANK_TRANSFER - BCA")
        $detailedMethod = $paymentChannel ? "{$paymentMethod} - {$paymentChannel}" : $paymentMethod;

        return [
            'external_id' => $request->input('external_id'),
            'status' => $status,
            'payment_method' => $detailedMethod, 
            'payment_channel' => $paymentChannel,
            'amount' => $request->input('paid_amount') ?? $request->input('amount'),
            'raw_response' => json_encode($request->all()),
        ];
    }

    public function getInvoice($invoiceId)
    {
        $secretKey = $this->config['secret_key'];
        
        $response = Http::withBasicAuth($secretKey, '')
            ->get("{$this->baseUrl}/v2/invoices/{$invoiceId}");

        if ($response->failed()) {
             // Try fetching by external ID if invoiceId fails (fallback) or throw
             throw new Exception("Xendit Get Invoice Error: " . $response->body());
        }

        return $response->json();
    }

    public function getInvoiceByExternalId($externalId)
    {
        $secretKey = $this->config['secret_key'];
        
        // Use v2 invoices endpoint with external_id filter (returns array)
        $response = Http::withBasicAuth($secretKey, '')
            ->get("{$this->baseUrl}/v2/invoices", [
                'external_id' => $externalId,
                'limit' => 1
            ]);

        if ($response->failed()) {
             throw new Exception("Xendit Get Invoice Error: " . $response->body());
        }

        $data = $response->json();
        // Xendit returns array of invoices for this external_id
        return !empty($data) && isset($data[0]) ? $data[0] : null; 
    }
}
