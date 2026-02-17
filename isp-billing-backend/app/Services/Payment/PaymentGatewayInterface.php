<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Create an invoice/payment link.
     *
     * @param string $externalId  Order ID from our system
     * @param float  $amount      Amount to charge
     * @param string $payerName   Customer Name
     * @param string $payerEmail  Customer Email
     * @param string $description Payment Description
     * @param array  $items       Optional line items
     * @return array              ['status' => 'success', 'payment_url' => '...', 'payment_token' => '...']
     */
    public function createInvoice($externalId, $amount, $payerName, $payerEmail, $description, $items = []);

    /**
     * Handle incoming webhook/callback.
     *
     * @param Request $request
     * @return array  Normalized data ['external_id' => ..., 'status' => 'paid|expired|failed', 'payment_method' => '...']
     */
    public function handleCallback(Request $request);
}
