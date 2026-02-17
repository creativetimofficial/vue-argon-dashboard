<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISPOrder;
use App\Models\Invoice;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get all orders for authenticated ISP
     */
    public function index(Request $request)
    {
        $ispId = Auth::user()->isp_id;

        $query = ISPOrder::where('isp_id', $ispId)
            ->with(['subscriptionPackage', 'invoice']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Option to hide expired orders
        if ($request->boolean('hide_expired')) {
            $query->where('status', '!=', 'expired');
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get single order details
     */
    public function show($id)
    {
        $ispId = Auth::user()->isp_id;

        $order = ISPOrder::where('isp_id', $ispId)
            ->with(['subscriptionPackage', 'invoice'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'domain_accessible' => $order->isDomainAccessible(),
                'can_retry_payment' => $order->canRetryPayment(),
            ]
        ]);
    }

    /**
     * Retry payment for expired/failed order
     */
    public function retryPayment($id, Request $request)
    {
        $ispId = Auth::user()->isp_id;

        $order = ISPOrder::where('isp_id', $ispId)->findOrFail($id);

        if (!$order->canRetryPayment()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be retried. Status must be expired or failed.'
            ], 400);
        }

        // Increment retry count
        $order->incrementRetryCount();

        // Create new invoice for this order
        $invoice = Invoice::create([
            'isp_id' => $ispId,
            'subscription_id' => $order->id,
            'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => $order->price,
            'tax' => 0,
            'discount' => 0,
            'total' => $order->price,
            'paid_amount' => 0,
            'status' => 'sent',
        ]);

        // Get payment gateway expiry duration
        $paymentGateway = PaymentGateway::where('is_active', true)->first();
        $expiryDuration = $paymentGateway ? $paymentGateway->expiry_duration : 24; // default 24 hours

        // Update order status and payment expiry
        $order->update([
            'status' => 'pending_payment',
            'payment_status' => 'pending',
            'payment_expired_at' => now()->addHours($expiryDuration),
            'payment_gateway' => $request->payment_gateway ?? $paymentGateway->name ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment retry initiated successfully',
            'data' => [
                'order' => $order->fresh(),
                'invoice' => $invoice,
            ]
        ]);
    }

    /**
     * Cancel pending order
     */
    public function cancel($id)
    {
        $ispId = Auth::user()->isp_id;

        $order = ISPOrder::where('isp_id', $ispId)->findOrFail($id);

        if (!in_array($order->status, ['pending_payment', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending or processing orders can be cancelled.'
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'domain_active' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order
        ]);
    }
}
