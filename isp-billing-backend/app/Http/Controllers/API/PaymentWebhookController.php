<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ISPOrder;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle payment webhook from payment gateway
     */
    public function handle(Request $request)
    {
        Log::info('Payment webhook received', $request->all());

        // Validate webhook signature (implement based on your payment gateway)
        // if (!$this->validateSignature($request)) {
        //     return response()->json(['error' => 'Invalid signature'], 403);
        // }

        $paymentReference = $request->input('payment_reference');
        $status = $request->input('status'); // success, failed, expired
        $amount = $request->input('amount');
        $transactionId = $request->input('transaction_id');

        // Find payment record
        $payment = Payment::where('payment_reference', $paymentReference)->first();

        if (!$payment) {
            Log::error('Payment not found', ['reference' => $paymentReference]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Find associated order
        $invoice = $payment->invoice;
        $order = $invoice ? ISPOrder::find($invoice->subscription_id) : null;

        switch ($status) {
            case 'success':
            case 'paid':
                $this->handleSuccessPayment($payment, $order, $transactionId);
                break;

            case 'failed':
                $this->handleFailedPayment($payment, $order);
                break;

            case 'expired':
                $this->handleExpiredPayment($payment, $order);
                break;

            default:
                Log::warning('Unknown payment status', ['status' => $status]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle successful payment
     */
    protected function handleSuccessPayment($payment, $order, $transactionId)
    {
        // Update payment status
        $payment->update([
            'status' => 'success',
            'payment_date' => now(),
            'payment_details' => json_encode([
                'transaction_id' => $transactionId,
                'processed_at' => now(),
            ]),
        ]);

        // Update invoice
        if ($payment->invoice) {
            $payment->invoice->update([
                'status' => 'paid',
                'paid_amount' => $payment->amount,
                'paid_at' => now(),
            ]);
        }

        // Activate order and domain
        if ($order) {
            $order->activateDomain();

            // Set expired_date based on billing cycle
            $expiredDate = $this->calculateExpiredDate($order->billing_cycle);
            $order->update(['expired_date' => $expiredDate]);

            Log::info('Order activated', [
                'order_id' => $order->id,
                'reference' => $order->reference,
                'domain_active' => true,
            ]);
        }
    }

    /**
     * Handle failed payment
     */
    protected function handleFailedPayment($payment, $order)
    {
        $payment->update(['status' => 'failed']);

        if ($order) {
            $order->update([
                'status' => 'failed',
                'payment_status' => 'unpaid',
                'domain_active' => false,
            ]);

            Log::info('Order payment failed', [
                'order_id' => $order->id,
                'reference' => $order->reference,
            ]);
        }
    }

    /**
     * Handle expired payment
     */
    protected function handleExpiredPayment($payment, $order)
    {
        $payment->update(['status' => 'cancelled']);

        if ($order) {
            $order->markAsExpired();

            Log::info('Order payment expired', [
                'order_id' => $order->id,
                'reference' => $order->reference,
            ]);
        }
    }

    /**
     * Calculate expired date based on billing cycle
     */
    protected function calculateExpiredDate($billingCycle)
    {
        switch ($billingCycle) {
            case 'daily':
                return now()->addDay();
            case 'weekly':
                return now()->addWeek();
            case 'monthly':
                return now()->addMonth();
            case 'quarterly':
                return now()->addMonths(3);
            case 'semi_annual':
                return now()->addMonths(6);
            case 'annual':
                return now()->addYear();
            default:
                return now()->addMonth();
        }
    }

    /**
     * Validate webhook signature (implement based on your payment gateway)
     */
    protected function validateSignature(Request $request)
    {
        // Implement signature validation based on your payment gateway
        // Example for Midtrans:
        // $serverKey = config('payment.midtrans.server_key');
        // $signatureKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        // return $signatureKey === $request->signature_key;

        return true; // For now, always return true
    }
}
