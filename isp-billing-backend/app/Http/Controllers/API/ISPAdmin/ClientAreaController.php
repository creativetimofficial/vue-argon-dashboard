<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\ISPService;
use App\Models\ISPOrder;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientAreaController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        $user = auth()->user();
        $isp = $user->isp;

        if (!$isp) {
            return response()->json([
                'balance' => 0,
                'active_services' => 0,
                'unpaid_invoices' => 0,
                'pending_orders' => 0,
            ]);
        }

        $balance = 0; // TODO: Implement wallet balance
        $activeServices = ISPOrder::where('isp_id', $isp->id)
            ->where('status', 'active')
            ->count();
        $unpaidInvoices = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->where('billable_id', $isp->id)
            ->where('payment_status', '!=', 'paid')
            ->count();
        $pendingOrders = ISPOrder::where('isp_id', $isp->id)
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'balance' => $balance,
            'active_services' => $activeServices,
            'unpaid_invoices' => $unpaidInvoices,
            'pending_orders' => $pendingOrders,
        ]);
    }

    /**
     * Get available services
     */
    public function services()
    {
        $services = ISPService::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($services);
    }

    /**
     * Get available subscription packages
     */
    public function packages()
    {
        $packages = \App\Models\SubscriptionPackage::where('is_active', true)
            ->orderBy('price_monthly')
            ->get();

        return response()->json($packages);
    }

    /**
     * Get ISP's orders
     */
    public function orders()
    {
        $user = auth()->user();
        $isp = $user->isp;

        if (!$isp) {
            return response()->json([]);
        }

        $orders = ISPOrder::where('isp_id', $isp->id)
            ->with(['service', 'subscriptionPackage'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Cancel order
     */
    public function cancelOrder($id)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $order = ISPOrder::where('isp_id', $isp->id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
        ]);
    }

    /**
     * Delete order
     */
    public function deleteOrder($id)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $order = ISPOrder::where('isp_id', $isp->id)
            ->where('id', $id)
            ->whereIn('status', ['pending', 'cancelled'])
            ->firstOrFail();

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }

    /**
     * Create new order
     */
    public function createOrder(Request $request)
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
            'service_id' => 'nullable|exists:isp_services,id',
            'package_id' => 'nullable|exists:subscription_packages,id',
            'billing_cycle' => 'nullable|in:monthly,quarterly,semi_annual,annual',
            'domain_type' => 'required|in:subdomain,custom',
            'subdomain' => 'required_if:domain_type,subdomain|string|max:50',
            'domain' => 'required_if:domain_type,custom|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $price = 0;
            $serviceId = $validated['service_id'] ?? null;
            $packageId = $validated['package_id'] ?? null;
            $billingCycle = $validated['billing_cycle'] ?? 'monthly';
            $isTrial = false;

            if ($serviceId) {
                $service = ISPService::findOrFail($serviceId);
                $price = $service->price;
                $isTrial = $service->trial_days > 0 || $service->price == 0;
            } elseif ($packageId) {
                $package = \App\Models\SubscriptionPackage::findOrFail($packageId);
                $price = $package->price_monthly; // Default to monthly for now
                // Package is trial if price is 0
                $isTrial = $package->price_monthly == 0; 
            } else {
                throw new \Exception('Please select a service or package.');
            }

            // Generate reference
            $reference = 'order-' . date('YmdHis') . rand(1000, 9999);

            // Generate domain
            $domain = null;
            if ($validated['domain_type'] === 'subdomain') {
                $domain = $validated['subdomain'] . '.' . $request->getHost();
            } else {
                $domain = $validated['domain'];
            }

            $order = ISPOrder::create([
                'isp_id' => $isp->id,
                'service_id' => $serviceId,
                'subscription_package_id' => $packageId,
                'reference' => $reference,
                'status' => 'pending',
                'payment_status' => $isTrial ? 'paid' : 'pending',
                'price' => $price,
                'billing_cycle' => $billingCycle,
                'domain_type' => $validated['domain_type'],
                'subdomain' => $validated['subdomain'] ?? null,
                'domain' => $domain,
                'notes' => $validated['notes'] ?? (($packageId) ? "Ordering Package: " . $package->name : null),
            ]);

            DB::commit();

            $responseData = [
                'success' => true,
                'message' => 'Order submitted successfully.',
                'order' => $order->load(['service', 'subscriptionPackage']),
            ];

            // If it's a paid package, generate payment URL
            if ($packageId && !$isTrial) {
                try {
                    $paymentGateway = PaymentGateway::where('is_active', true)
                        ->orderBy('is_default', 'desc')
                        ->first();

                    if ($paymentGateway) {
                        $paymentUrl = $this->createPayment($isp, $package, $price, $billingCycle, $paymentGateway, $order);
                        $responseData['payment_url'] = $paymentUrl;
                        $responseData['message'] = 'Order created. Redirecting to payment...';
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to create payment for order: ' . $e->getMessage());
                    // We still have the order, but payment URL generation failed
                    $responseData['message'] = 'Order created, but failed to generate payment URL. Please contact admin.';
                }
            } elseif ($packageId && $isTrial) {
                $responseData['message'] = 'Trial order submitted. Waiting for super admin approval.';
            }

            return response()->json($responseData, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ISP's services (active orders)
     */
    public function myServices()
    {
        $user = auth()->user();
        $isp = $user->isp;

        if (!$isp) {
            return response()->json([]);
        }

        $orders = ISPOrder::where('isp_id', $isp->id)
            ->whereIn('status', ['active', 'pending', 'approved'])
            ->with(['service', 'subscriptionPackage'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Get service detail
     */
    public function serviceDetail($id)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $order = ISPOrder::where('isp_id', $isp->id)
            ->where('id', $id)
            ->with(['service', 'subscriptionPackage'])
            ->firstOrFail();

        return response()->json($order);
    }

    /**
     * Update service (notes, auto_renew)
     */
    public function updateService(Request $request, $id)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $order = ISPOrder::where('isp_id', $isp->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'notes' => 'nullable|string|max:50',
            'auto_renew' => 'boolean',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'service' => $order->load('service'),
        ]);
    }

    /**
     * Get invoices
     */
    public function invoices()
    {
        $user = auth()->user();
        $isp = $user->isp;

        if (!$isp) {
            return response()->json([]);
        }

        $invoices = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->where('billable_id', $isp->id)
            ->with('payment')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($invoices);
    }

    /**
     * Get invoice detail
     */
    public function invoiceDetail($id)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $invoice = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->where('billable_id', $isp->id)
            ->where('id', $id)
            ->with(['items', 'payment'])
            ->firstOrFail();

        return response()->json($invoice);
    }

    /**
     * Get profile
     */
    public function profile()
    {
        $user = auth()->user();
        $isp = $user->isp;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $isp->address ?? '',
            'whatsapp' => $user->phone ?? '',
            'referral_code' => $isp->referral_code ?? Str::upper(Str::random(5)),
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8|confirmed',
            'address' => 'nullable|string',
            'whatsapp' => 'nullable|string|max:20',
        ]);

        if (isset($validated['name'])) {
            $user->update(['name' => $validated['name']]);
        }

        if (isset($validated['password'])) {
            $user->update(['password' => \Hash::make($validated['password'])]);
        }

        if (isset($validated['address']) && $isp) {
            $isp->update(['address' => $validated['address']]);
        }

        if (isset($validated['whatsapp']) && $isp) {
            $isp->update(['phone' => $validated['whatsapp']]);
            $user->update(['phone' => $validated['whatsapp']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }

    /**
     * Get balance
     */
    public function balance()
    {
        // TODO: Implement wallet balance
        return response()->json([
            'balance' => 0,
        ]);
    }

    /**
     * Get topup history
     */
    public function topupHistory()
    {
        // TODO: Implement topup history
        return response()->json([]);
    }

    /**
     * Update referral code
     */
    public function updateReferralCode(Request $request)
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
            'referral_code' => 'required|string|max:10|unique:isps,referral_code,' . $isp->id,
        ]);

        $isp->update(['referral_code' => Str::upper($validated['referral_code'])]);

        return response()->json([
            'success' => true,
            'message' => 'Referral code updated successfully',
        ]);
    }

    /**
     * Create payment transaction for package order
     */
    private function createPayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order)
    {
        // Get Server Key - try multiple sources
        $serverKey = $paymentGateway->secret_key 
            ?? ($paymentGateway->settings['server_key'] ?? null)
            ?? $paymentGateway->merchant_id; // Fallback to merchant_id if it contains server key
        
        if (empty($serverKey)) {
            throw new \Exception('Payment Gateway Server Key not configured. Please contact administrator to configure Payment Gateway with Server Key.');
        }
        
        // Route to appropriate payment gateway handler
        $gatewayName = strtolower($paymentGateway->gateway_name);
        
        switch ($gatewayName) {
            case 'midtrans':
                return $this->createMidtransPayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order, $serverKey);
            
            case 'xendit':
                return $this->createXenditPayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order, $serverKey);
            
            case 'stripe':
                return $this->createStripePayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order, $serverKey);
            
            default:
                throw new \Exception("Payment gateway '{$paymentGateway->gateway_name}' is not yet supported. Please contact administrator.");
        }
    }
    
    /**
     * Create Midtrans payment transaction
     */
    private function createMidtransPayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order, $serverKey)
    {
        // Configure Midtrans
        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = !$paymentGateway->sandbox_mode;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
        
        // For development: disable SSL verification if configured
        if (config('app.env') !== 'production' && env('MIDTRANS_VERIFY_SSL', 'true') === 'false') {
            \Midtrans\Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            \Midtrans\Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
        }

        $orderId = 'ORD-' . $order->id . '-' . time();
        
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
                    'name' => 'Package: ' . $package->name . ' (' . $billingCycle . ')',
                ]
            ],
            'callbacks' => [
                'finish' => config('app.frontend_url', 'http://localhost:8080') . '/payment-callback?status=success&order_id=' . $order->id,
                'error' => config('app.frontend_url', 'http://localhost:8080') . '/payment-callback?status=failed',
            ]
        ];

        try {
            $snapResponse = \Midtrans\Snap::createTransaction($params);
            return $snapResponse->redirect_url;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            throw new \Exception('Failed to create Midtrans payment: ' . $e->getMessage());
        }
    }
    
    /**
     * Create Xendit payment transaction
     */
    private function createXenditPayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order, $serverKey)
    {
        // Xendit implementation placeholder
        // TODO: Implement Xendit payment creation
        throw new \Exception('Xendit payment gateway is not yet implemented. Coming soon!');
    }
    
    /**
     * Create Stripe payment transaction
     */
    private function createStripePayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order, $serverKey)
    {
        // Stripe implementation placeholder
        // TODO: Implement Stripe payment creation
        throw new \Exception('Stripe payment gateway is not yet implemented. Coming soon!');
    }
}
