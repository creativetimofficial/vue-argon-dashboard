<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\SubscriptionPackage;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ISPSubscriptionController extends Controller
{
    /**
     * Subscribe to a package
     */
    public function subscribe(Request $request)
    {
        $user = auth()->user();
        $isp = $user->isp;

        if (!$isp) {
            return response()->json([
                'success' => false,
                'message' => 'ISP not found'
            ], 404);
        }

        $validated = $request->validate([
            'package_id' => 'required|exists:subscription_packages,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $package = SubscriptionPackage::findOrFail($validated['package_id']);

        if (!$package->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Package is not available'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Check if package is trial (price is 0 or has trial_days)
            $isTrial = $package->price_monthly == 0 || ($package->trial_days ?? 0) > 0;

            if ($isTrial) {
                // For trial packages, set approval status to pending
                $isp->update([
                    'subscription_package_id' => $package->id,
                    'subscription_status' => 'trial',
                    'approval_status' => 'pending',
                    'subscription_start_date' => null,
                    'subscription_end_date' => null,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Trial package request submitted. Waiting for super admin approval.',
                    'requires_approval' => true,
                ]);
            } else {
                // For paid packages, create payment
                $price = $validated['billing_cycle'] === 'yearly' 
                    ? $package->price_yearly 
                    : $package->price_monthly;

                // Get active payment gateway
                $paymentGateway = PaymentGateway::where('is_active', true)
                    ->orderBy('is_default', 'desc')
                    ->first();

                if (!$paymentGateway) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No payment gateway configured. Please contact administrator.'
                    ], 400);
                }

                // Create payment transaction
                $paymentUrl = $this->createPayment($isp, $package, $price, $validated['billing_cycle'], $paymentGateway);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment gateway...',
                    'payment_url' => $paymentUrl,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create payment transaction
     */
    private function createPayment($isp, $package, $amount, $billingCycle, $paymentGateway)
    {
        // Configure Midtrans
        \Midtrans\Config::$serverKey = $paymentGateway->secret_key ?? ($paymentGateway->settings['server_key'] ?? '');
        \Midtrans\Config::$isProduction = $paymentGateway->sandbox_mode ? false : true;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'SUB-' . $isp->id . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $isp->name,
                'email' => $isp->email ?? auth()->user()->email,
            ],
            'item_details' => [
                [
                    'id' => $package->id,
                    'price' => (int) $amount,
                    'quantity' => 1,
                    'name' => 'Subscription: ' . $package->name . ' (' . $billingCycle . ')',
                ]
            ],
            'callbacks' => [
                'finish' => config('app.frontend_url', 'http://localhost:8080') . '/payment-callback?status=success&isp_id=' . $isp->id . '&package_id=' . $package->id . '&amount=' . $amount . '&billing_cycle=' . $billingCycle,
                'error' => config('app.frontend_url', 'http://localhost:8080') . '/payment-callback?status=failed',
            ]
        ];

        try {
            $snapResponse = \Midtrans\Snap::createTransaction($params);
            return $snapResponse->redirect_url;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            throw new \Exception('Failed to generate payment URL: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment callback
     */
    public function paymentCallback(Request $request)
    {
        $validated = $request->validate([
            'isp_id' => 'required|exists:isps,id',
            'package_id' => 'required|exists:subscription_packages,id',
            'amount' => 'required|numeric',
            'billing_cycle' => 'required|in:monthly,yearly',
            'status' => 'required|in:success,failed',
            'transaction_id' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $isp = ISP::findOrFail($validated['isp_id']);
            $package = SubscriptionPackage::findOrFail($validated['package_id']);

            if ($validated['status'] === 'success') {
                // Calculate subscription dates
                $startDate = now();
                $endDate = $validated['billing_cycle'] === 'yearly' 
                    ? $startDate->copy()->addYear() 
                    : $startDate->copy()->addMonth();

                // Activate subscription
                $isp->update([
                    'subscription_package_id' => $package->id,
                    'subscription_status' => 'active',
                    'approval_status' => 'approved',
                    'subscription_start_date' => $startDate,
                    'subscription_end_date' => $endDate,
                    'is_active' => true,
                ]);

                // Activate user
                $isp->users()->update(['is_active' => true]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful. Package activated.',
                    'redirect_url' => config('app.frontend_url', 'http://localhost:8080') . '/isp-admin/dashboard',
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed. Please try again.',
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment callback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment callback.'
            ], 500);
        }
    }
}
