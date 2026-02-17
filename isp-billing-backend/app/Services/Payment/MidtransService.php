<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class MidtransService implements PaymentGatewayInterface
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->configureMidtrans();
    }

    private function configureMidtrans()
    {
        // Set your Merchant Server Key
        Config::$serverKey = $this->config['secret_key'];
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = !$this->config['sandbox_mode'];
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;
        
        // Disable SSL verification if in dev environment and configured to do so (mirrors existing logic)
        if (config('app.env') !== 'production' && env('MIDTRANS_VERIFY_SSL', 'true') === 'false') {
            Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
        }
    }

    public function createInvoice($externalId, $amount, $payerName, $payerEmail, $description, $items = [])
    {
        $params = [
            'transaction_details' => [
                'order_id' => $externalId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $payerName,
                'email' => $payerEmail,
            ],
            // Map generic items to Midtrans format if needed, or pass empty if not strict
            // Midtrans items: id, price, quantity, name
            'item_details' => array_map(function($item) {
                 return [
                     'id' => $item['id'] ?? 'ITEM-' . rand(100,999),
                     'price' => (int) $item['price'],
                     'quantity' => (int) $item['quantity'],
                     'name' => substr($item['name'], 0, 50),
                 ];
            }, $items),
            'callbacks' => [
                'finish' => config('app.frontend_url') . '/payment-callback?status=success&order_id=' . $externalId,
                'error' => config('app.frontend_url') . '/payment-callback?status=failed&order_id=' . $externalId,
            ],
        ];

        try {
            $snapResponse = Snap::createTransaction($params);
            
            return [
                'status' => 'success',
                'payment_url' => $snapResponse->redirect_url,
                'payment_token' => $snapResponse->token,
                'raw_response' => $snapResponse
            ];
        } catch (Exception $e) {
            throw new Exception("Midtrans Create Transaction Failed: " . $e->getMessage());
        }
    }

    public function handleCallback(Request $request)
    {
        // Midtrans Logic
        // Verify signature usually involves checking the hash of order_id + status + amount + serverKey
        // But simply reading the status is the first step.
        // Midtrans notification comes as JSON body.
        
        $notification = new \Midtrans\Notification(); 
        // Note: The \Midtrans\Notification() constructor automatically reads php://input. 
        // If Request $request is passed, we might need to manually handle validation if we want strictness.
        // However, standard Midtrans lib usage reads input stream.
        
        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;

        $status = 'pending';
        if ($transaction == 'capture') {
            if ($fraud == 'challenge') {
                $status = 'pending'; // challenge
            } else {
                $status = 'paid';
            }
        } else if ($transaction == 'settlement') {
            $status = 'paid';
        } else if ($transaction == 'pending') {
            $status = 'pending';
        } else if ($transaction == 'deny') {
            $status = 'failed';
        } else if ($transaction == 'expire') {
            $status = 'expired';
        } else if ($transaction == 'cancel') {
            $status = 'failed';
        }

        return [
            'external_id' => $orderId,
            'status' => $status,
            'payment_method' => $type, // e.g., bank_transfer, credit_card
            'amount' => $notification->gross_amount,
            'raw_response' => json_encode($notification),
        ];
    }
}
