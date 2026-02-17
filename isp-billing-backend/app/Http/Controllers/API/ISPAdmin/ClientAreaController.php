<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\ISPOrder;
use App\Models\SubscriptionPackage;
use App\Models\ISPService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceCreated;
use App\Mail\PackageActive;
use App\Mail\PackageInactive;

class ClientAreaController extends Controller
{
    /**
     * Get client area statistics
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

        $balance = $isp->balance;
        $activeServices = ISPOrder::where('isp_id', $isp->id)
            ->where('status', 'active')
            ->count();
        $unpaidInvoices = Invoice::where('isp_id', $isp->id)
            ->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->count();
        $pendingOrders = ISPOrder::where('isp_id', $isp->id)
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'balance' => $balance,
            'active_services' => $activeServices,
            'unpaid_invoices' => $unpaidInvoices,
            'pending_orders' => $pendingOrders,
            'isp' => [
                'subscription_status' => $isp->subscription_status,
                'subscription_end_date' => $isp->subscription_end_date,
                'package_name' => $isp->subscriptionPackage?->name ?? 'No Package',
                'package' => $isp->subscriptionPackage,
                'is_active' => $isp->is_active,
            ]
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
            ->orderBy('price')
            ->get();

        return response()->json($packages);
    }

    /**
     * Get available servers
     */
    public function getServers()
    {
        $servers = \App\Models\Server::where('is_active', true)
            ->select('id', 'name', 'ip_address', 'location', 'capacity', 'current_users', 'is_active')
            ->orderBy('name')
            ->get();

        return response()->json($servers);
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
            ->whereIn('status', ['pending_payment', 'expired', 'failed']) // Allow cancel for these statuses
            ->firstOrFail();

        $order->update(['status' => 'cancelled']);

        // Send Email
        try {
            Mail::to($isp->email)->send(new PackageInactive($order, 'cancelled'));
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email: ' . $e->getMessage());
        }

        // Sync: Cancel associated invoice
        $invoice = Invoice::where('subscription_id', $order->id)
            ->where('billable_type', 'App\\Models\\ISP')
            ->first();
            
        if ($invoice && $invoice->payment_status === 'unpaid') {
            // Only update status column (payment_status enum doesn't have 'cancelled')
            $invoice->update(['status' => 'cancelled']);
        }

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
            ->whereIn('status', ['pending_payment', 'cancelled'])
            ->firstOrFail();

        // Sync: Delete associated invoice
        $invoice = Invoice::where('subscription_id', $order->id)
            ->where('billable_type', 'App\\Models\\ISP')
            ->first();
            
        if ($invoice) {
            // Check if we can safely delete (e.g. only if unpaid)
            // But since we are deleting the order, we usually assume we can delete the invoice unless it's paid/legal requirement.
            // For now, let's delete it to keep clean state as requested.
            $invoice->delete();
        }

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

        // Validate Request
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'service_id' => 'nullable|exists:isp_services,id',
            'package_id' => 'nullable|exists:subscription_packages,id', // Fallback
            'billing_cycle' => 'nullable|string',
            'domain_type' => 'required|in:subdomain,custom',
            'subdomain' => 'nullable|string|min:3|max:50',
            'domain' => 'nullable|string|min:4|max:100',
            'notes' => 'nullable|string|max:500',
            'referral_code' => 'nullable|string|exists:isps,referral_code',
        ]);

        // Validate Referral Code
        $referrerId = null;
        $discount = 0;
        if (!empty($validated['referral_code'])) {
            $referrer = \App\Models\ISP::where('referral_code', $validated['referral_code'])->first();
            if ($referrer && $referrer->id !== $isp->id) {
                $referrerId = $referrer->id;
                $discount = 0.10; // 10% Discount
            } elseif ($referrer && $referrer->id === $isp->id) {
                 return response()->json([
                    'success' => false,
                    'message' => 'You cannot use your own referral code.'
                ], 422);
            }
        }

        // Custom Validation: Uniqueness for Active Orders
        if ($validated['domain_type'] === 'subdomain' && !empty($validated['subdomain'])) {
            $exists = \App\Models\ISPOrder::where('subdomain', $validated['subdomain'])
                ->whereIn('status', ['active', 'pending_payment', 'paid']) // Check against active/pending_payment
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subdomain "' . $validated['subdomain'] . '" is already taken not available.'
                ], 422);
            }
        }
        if ($validated['domain_type'] === 'custom' && !empty($validated['domain'])) {
             $exists = \App\Models\ISPOrder::where('domain', $validated['domain'])
                ->whereIn('status', ['active', 'pending_payment', 'paid'])
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Domain "' . $validated['domain'] . '" is already registered or processing.'
                ], 422);
            }
        }

        // Check Server Capacity
        $server = \App\Models\Server::findOrFail($validated['server_id']);
        if ($server->capacity > 0 && $server->current_users >= $server->capacity) {
             return response()->json([
                'success' => false,
                'message' => 'Selected server is full. Please choose another server.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $serviceId = $validated['service_id'] ?? null;
            $packageId = $validated['package_id'] ?? null;
            $billingCycle = $validated['billing_cycle'] ?? 'monthly';
            $paymentStatus = 'unpaid';

            $requiresApproval = false;
            if ($serviceId) {
                $service = ISPService::findOrFail($serviceId);
                $price = $service->price;
                $requiresApproval = $service->requires_manual_approval;
                $isTrial = $service->trial_days > 0 || $service->price == 0;
            } elseif ($packageId) {
                $package = \App\Models\SubscriptionPackage::findOrFail($packageId);
                
                // Get price
                $price = $package->price;
                
                $requiresApproval = $package->requires_manual_approval;
                // Package is trial if price is 0 or it's named 'trial'
                $isTrial = $price == 0 || $package->slug === 'trial'; 
            } else {
                throw new \Exception('Please select a service or package.');
            }

            // Apply Referral Discount
            if ($discount > 0 && !$isTrial) {
                $price = $price - ($price * $discount);
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

            $status = 'pending_payment';

            // Auto-approve if not trial or explicitly set to not require manual approval
            if ($isTrial && !$requiresApproval) {
                $status = 'active';
            }

            
            // Get payment gateway for expiry duration
            $paymentGateway = null;
            $paymentExpiredAt = null;
            if (!$isTrial && $status === 'pending_payment') {
                $paymentGateway = PaymentGateway::where('is_active', true)
                    ->orderBy('is_default', 'desc')
                    ->first();
                
                if ($paymentGateway) {
                    $expiryHours = $paymentGateway->expiry_duration ?? 24;
                    $paymentExpiredAt = now()->addHours($expiryHours);
                }
            }

            $order = ISPOrder::create([
                'isp_id' => $isp->id,
                'referrer_id' => $referrerId,
                'isp_service_id' => $serviceId,
                'subscription_package_id' => $packageId,
                'server_id' => $validated['server_id'],
                'reference' => $reference,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'price' => $price,
                'billing_cycle' => $billingCycle,
                'service_name' => $serviceId ? $service->name : $package->name,
                'domain_type' => $validated['domain_type'],
                'subdomain' => $validated['subdomain'] ?? null,
                'domain' => $domain,
                'notes' => $validated['notes'] ?? (($packageId) ? "Ordering Package: " . $package->name : null),
                'payment_expired_at' => $paymentExpiredAt,
                'payment_gateway' => $paymentGateway ? $paymentGateway->gateway_name : null,
                'domain_active' => false, // Will be activated after payment
            ]);

            // If auto-activated, generate credentials or update ISP subscription
            // Only provision if ACTIVE and (Trial OR Paid)
            if ($status === 'active' && ($isTrial || $paymentStatus === 'paid')) {
                if ($serviceId || $packageId) {
                    $username = date('YmdHis') . rand(100, 999);
                    // Centralized generation in model
                    $order->generateCredentials();
                    
                    if ($packageId) {
                        // Calculate days based on package setup
                        $totalDays = $package->active_days ?: 30;
                        
                        $isp->update([
                            'subscription_package_id' => $packageId,
                            'subscription_status' => $isTrial ? 'trial' : 'active',
                            'subscription_start_date' => now(),
                            'subscription_end_date' => now()->addDays($totalDays),
                            'billing_cycle' => $billingCycle,
                            'is_active' => true,
                        ]);
                        
                        $order->update([
                            'start_date' => now(),
                            'expired_date' => now()->addDays($totalDays),
                            'approved_at' => now(),
                        ]);
                    } elseif ($serviceId) {
                         $days = $service->trial_days ?: 30;
                         $order->update([
                            'start_date' => now(),
                            'expired_date' => now()->addDays($days),
                            'approved_at' => now(),
                        ]);
                    }
                }
            }

            // Create Invoice ONLY if:
            // 1. Trial order (will be activated immediately or after approval)
            // 2. Paid order WITHOUT manual approval (goes straight to payment)
            // Skip invoice for paid orders requiring approval (will be created after approval)
            $invoiceId = null;
            if ($isTrial || (!$isTrial && !$requiresApproval)) {
                // Create Invoice
                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                    'billable_type' => 'App\\Models\\ISP',
                    'billable_id' => $isp->id,
                    'isp_id' => $isp->id,
                    'subscription_id' => $order->id,
                    'subtotal' => $price,
                    'tax' => 0,
                    'discount' => 0,
                    'total' => $price,
                    'payment_status' => $paymentStatus === 'paid' ? 'paid' : 'unpaid',
                    'issue_date' => now(),
                    'due_date' => now()->addDays(3),
                    'payment_method' => null, // Will be updated on payment
                    'notes' => 'Generated automatically for order ' . $order->reference
                ]);

                // Create Invoice Item
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $serviceId ? "Service: " . $service->name : "Package: " . $package->name,
                    'quantity' => 1,
                    'unit_price' => $price,
                    'total' => $price,
                ]);
                
                $invoiceId = $invoice->id;

                 // Send Email
                try {
                    Mail::to($isp->email)->send(new InvoiceCreated($invoice, $isp));
                } catch (\Exception $e) {
                    Log::error('Failed to send invoice email: ' . $e->getMessage());
                }
            }

            DB::commit();

            $responseData = [
                'success' => true,
                'message' => 'Order submitted successfully.',
                'order' => $order->load(['service', 'subscriptionPackage']),
            ];

            if ($packageId && $isTrial) {
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

        // Lazy Generation of Credentials for existing active orders
        if ($order->status === 'active' && empty($order->username)) {
            $order->generateCredentials();
            // Reload to get updated attributes
            $order->refresh();
        }

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
     * Cancel invoice
     */
    public function cancelInvoice($id)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $invoice = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->where('billable_id', $isp->id)
            ->where('id', $id)
            ->where('payment_status', 'unpaid')
            ->firstOrFail();

        // Only update status column to 'cancelled'
        // Frontend will check status === 'cancelled' to display CANCELLED badge
        $invoice->update(['status' => 'cancelled']);

        // Sync: If linked to Order, cancel order too
        if ($invoice->subscription_id) {
             $order = ISPOrder::find($invoice->subscription_id);
             if ($order && in_array($order->status, ['pending_payment', 'expired', 'failed'])) {
                 $order->update(['status' => 'cancelled']);
                 
                 // Send Email
                try {
                    Mail::to($isp->email)->send(new PackageInactive($order, 'cancelled'));
                } catch (\Exception $e) {
                    Log::error('Failed to send cancellation email: ' . $e->getMessage());
                }
             }
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice cancelled successfully',
        ]);
    }

    /**
     * Initiate payment for an existing invoice
     */
    public function payInvoice(Request $request, $id)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $invoice = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->where('billable_id', $isp->id)
            ->where('id', $id)
            ->where('payment_status', 'unpaid')
            ->firstOrFail();

        // Get active payment gateway
        $paymentGateway = PaymentGateway::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->first();

        if (!$paymentGateway) {
            return response()->json(['message' => 'No payment gateway configured'], 400);
        }

        // Handle Pay with Balance
        if ($request->payment_method === 'balance') {
            if ($isp->balance >= $invoice->total) {
                try {
                    DB::beginTransaction();
                    
                    // Deduct balance
                    $isp->decrement('balance', $invoice->total);
                    
                    // Generate Transaction ID
                    $transactionId = 'BAL-INV-' . $invoice->id . '-' . time();
                    
                    // Create Payment Record
                    \App\Models\Payment::create([
                        'isp_id' => $isp->id,
                        'invoice_id' => $invoice->id,
                        'payment_reference' => $transactionId,
                        'transaction_id' => $transactionId,
                        'payment_gateway' => 'Balance',
                        'payment_method' => 'balance',
                        'amount' => $invoice->total,
                        'status' => 'success',
                        'paid_at' => now(),
                        'notes' => 'Paid using account balance'
                    ]);
                    
                    // Update Invoice
                    $invoice->update([
                        'payment_status' => 'paid',
                        'payment_method' => 'balance',
                        'paid_amount' => $invoice->total,
                        'paid_at' => now()
                    ]);
                    
                    // Update Order/Subscription if applicable
                    if ($invoice->subscription_id) {
                        $order = ISPOrder::find($invoice->subscription_id);
                        if ($order) {
                            // Centralized Activation and Provisioning
                            $order->activateDomain();
                            
                            $days = 30;
                             if ($order->service_id) {
                                $days = $order->service->trial_days ?: 30;
                            } elseif ($order->subscription_package_id) {
                                $days = $order->subscriptionPackage->active_days ?? 30;
                            }
                            
                            $newStart = now();
                            $newEnd = now()->addDays($days);
                            
                            $order->update([
                                'start_date' => $newStart,
                                'expired_date' => $newEnd
                            ]);

                            // Send Active Email
                            try {
                                Mail::to($isp->email)->send(new PackageActive($order));
                            } catch (\Exception $e) {
                                Log::error('Failed to send active package email: ' . $e->getMessage());
                            }
                            
                             // If package
                            if ($order->subscription_package_id) {
                                $isp->update([
                                    'subscription_package_id' => $order->subscription_package_id,
                                    'subscription_status' => 'active',
                                    'subscription_start_date' => $newStart,
                                    'subscription_end_date' => $newEnd,
                                    'is_active' => true,
                                ]);
                            }
                        }
                    }
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment successful using balance',
                        'refresh' => true
                    ]);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['message' => 'Balance payment failed: ' . $e->getMessage()], 500);
                }
            } else {
                 return response()->json(['message' => 'Insufficient balance'], 400);
            }
        }

        // Check for existing pending payment
        $pendingPayment = Payment::where('invoice_id', $invoice->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($pendingPayment && !empty($pendingPayment->payment_details['redirect_url'])) {
            // Check status with Gateway directly (Smart Auto-Correction)
            $statusCheck = $this->checkPendingPaymentStatus($pendingPayment, $paymentGateway);
            
            if ($statusCheck['status'] === 'active') {
                return response()->json([
                    'success' => true,
                    'payment_url' => $pendingPayment->payment_details['redirect_url'],
                    'payment_token' => $pendingPayment->payment_details['token'] ?? null // Return token
                ]);
            } elseif ($statusCheck['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment already successful',
                    'refresh' => true 
                ]);
            }
            // If expired or failed, we fall through to create new payment
        }

        // We need an order object or similar to pass to createPayment
        // The current createPayment expects an order object for Midtrans metadata
        $order = ISPOrder::find($invoice->subscription_id);
        
        try {
            // Generate unique Transaction ID
            $transactionId = 'ORD-INV-' . $invoice->id . '-' . time();
            
            // Create Payment Record (Pending)
            $payment = Payment::create([
                'isp_id' => $isp->id,
                'invoice_id' => $invoice->id,
                'payment_reference' => $transactionId,
                'transaction_id' => $transactionId, // Ensure we store this for callbacks
                'amount' => $invoice->total, // Expected valid total
                'payment_method' => $paymentGateway->gateway_name,
                'payment_gateway' => $paymentGateway->gateway_name,
                'status' => 'pending',
                'payment_date' => now(),
                'notes' => 'Generated via ' . $paymentGateway->gateway_name,
            ]);

            // Use Factory to get service
            $gatewayService = \App\Factories\PaymentGatewayFactory::create($paymentGateway->gateway_name);
            
            $result = $gatewayService->createInvoice(
                $transactionId, 
                $invoice->total, 
                $isp->name, 
                $isp->email ?? auth()->user()->email, 
                'Invoice #' . $invoice->invoice_number,
                [
                    [
                        'id' => 'INV-'.$invoice->id, 
                        'price' => $invoice->total, 
                        'quantity' => 1, 
                        'name' => 'Invoice Payment #' . $invoice->invoice_number
                    ]
                ]
            );
            
            // Update Payment with URL
            $payment->update([
                'payment_details' => [
                    'redirect_url' => $result['payment_url'],
                    'token' => $result['payment_token'] ?? null,
                    'raw_response' => $result['raw_response'] ?? []
                ],
                'expiry_time' => now()->addMinutes($paymentGateway->expiry_duration ?: 1440)
            ]);
            
            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'payment_token' => $result['payment_token'] ?? null,
                'gateway' => $paymentGateway->gateway_name,
                'payment_method' => $paymentGateway->gateway_name,
                'client_key' => $paymentGateway->api_key // Return Client Key for Frontend JS (Snap)
            ]);
        } catch (\Exception $e) {
            // If payment creation failed, mark as failed
            if (isset($payment)) {
                $payment->update(['status' => 'failed', 'notes' => 'Failed: ' . $e->getMessage()]);
            }
            return response()->json(['message' => 'Failed to initiate payment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Topup balance
     */
    /**
     * Topup balance
     */
    public function topup(Request $request)
    {
        $user = auth()->user();
        $isp = $user->isp;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000',
            'method' => 'nullable|string', // Optional now
        ]);

        $paymentGateway = null;
        
        // If method provided, search by name
        if (!empty($validated['method'])) {
            $paymentGateway = PaymentGateway::where('is_active', true)
                ->where('gateway_name', 'like', $validated['method'])
                ->first();
        }
            
        // Fallback to default if not found
        if (!$paymentGateway) {
            $paymentGateway = PaymentGateway::where('is_active', true)
                 ->orderBy('is_default', 'desc')
                 ->first();
        }

        if (!$paymentGateway) {
             return response()->json(['message' => 'No active payment gateway available'], 400);
        }

        try {
            $paymentUrl = null;
            $paymentToken = null;
            
            // OPTIMIZATION: Check for existing pending topup with same amount within 30 minutes
            $existingPayment = Payment::where('isp_id', $isp->id)
                ->where('status', 'pending')
                ->where('amount', $validated['amount'])
                ->whereNull('invoice_id') // Ensure it's a topup
                ->where('created_at', '>=', now()->subMinutes(30))
                ->latest()
                ->first();

            if ($existingPayment) {
                // Reuse existing payment
                $transactionId = $existingPayment->transaction_id;
                
                // Update timestamp to keep it "fresh"
                $existingPayment->touch();
                
                // Strategy: Update Transaction ID slightly to force new Token from Midtrans if needed
                // But generally reusing the same is better for "Resume Payment"
                // For this implementation, we will try to reuse everything.
                
                $payment = $existingPayment;
                
            } else {
                // Create New
                $transactionId = 'TOP-' . $isp->id . '-' . time();
                
                $payment = Payment::create([
                    'isp_id' => $isp->id,
                    'invoice_id' => null,
                    'payment_reference' => $transactionId,
                    'transaction_id' => $transactionId,
                    'payment_gateway' => $paymentGateway->gateway_name,
                    'payment_method' => $paymentGateway->gateway_name, // Generic
                    'amount' => $validated['amount'],
                    'status' => 'pending',
                    'payment_date' => now(),
                    'notes' => 'Topup via ' . $paymentGateway->gateway_name
                ]);
            }

            // Use Factory
            $gatewayService = \App\Factories\PaymentGatewayFactory::create($paymentGateway->gateway_name);
            
            $result = $gatewayService->createInvoice(
                 $payment->transaction_id,
                 $validated['amount'],
                 $isp->name,
                 $isp->email ?? auth()->user()->email,
                 'Balance Topup',
                 [
                     [
                         'id' => 'TOPUP', 
                         'price' => $validated['amount'], 
                         'quantity' => 1, 
                         'name' => 'Balance Topup'
                     ]
                 ]
            );
            
            $paymentUrl = $result['payment_url'];
            $paymentToken = $result['payment_token'] ?? null;


            return response()->json([
                'success' => true,
                'message' => 'Topup initiated',
                'payment_url' => $paymentUrl,
                'payment_token' => $paymentToken ?? null,
                'gateway' => $paymentGateway->gateway_name,
                'client_key' => $paymentGateway->api_key // For Snap
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Topup failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get profile
     */
    public function profile()
    {
        $user = auth()->user();
        $isp = $user->isp;

        if (!$isp) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => '',
                'whatsapp' => $user->phone ?? '',
                'referral_code' => '',
            ]);
        }

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
 * Update referral code - DEPRECATED/REMOVED
 */
public function updateReferralCode(Request $request)
{
    return response()->json([
        'success' => false,
        'message' => 'Referral code update is disabled.'
    ], 403);
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

        if (isset($validated['whatsapp'])) {
            $user->update(['phone' => $validated['whatsapp']]);
            if ($isp) {
                $isp->update(['phone' => $validated['whatsapp']]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }

    /**
     * Request Withdrawal
     */
    public function withdrawRequest(Request $request)
    {
        $user = auth()->user();
        $isp = $user->isp;

        if (!$isp) {
             return response()->json(['message' => 'ISP profile not found'], 404);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:500000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        if ($isp->balance < $validated['amount']) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        try {
            DB::beginTransaction();

            // Deduct balance immediately
            $isp->decrement('balance', $validated['amount']);

            // Create pending withdrawal
            \App\Models\Withdrawal::create([
                'isp_id' => $isp->id,
                'amount' => $validated['amount'],
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
                'status' => 'pending_payment',
                'notes' => 'Withdrawal request by user'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'balance' => $isp->refresh()->balance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Withdrawal failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get current balance
     */
    public function balance()
    {
        $user = auth()->user();
        $isp = $user->isp;
        
        return response()->json([
            'balance' => $isp ? $isp->balance : 0
        ]);
    }

    /**
     * Get Withdrawal History
     */
    public function withdrawalHistory(Request $request)
    {
        $user = auth()->user();
        $isp = $user->isp;

        if (!$isp) {
            return response()->json([]);
        }

        $perPage = $request->input('per_page', 10);
        if ($perPage == 'all') $perPage = 1000;

        $history = Withdrawal::where('isp_id', $isp->id)
            ->orderBy('created_at', 'desc')
            ->paginate((int)$perPage);

        return response()->json($history);
    }

    /**
     * Get topup history
     */
    public function topupHistory(Request $request)
    {
        $user = auth()->user();
        $isp = $user->isp;
        
        if (!$isp) {
            return response()->json([]);
        }

        // AUTO-VERIFY PENDING TOPUPS (Lazy Load Fix for Localhost)
        try {
            $pendingTopups = Payment::where('isp_id', $isp->id)
                ->where('status', 'pending')
                ->where('transaction_id', 'like', 'TOP-%')
                ->where('created_at', '>=', now()->subDays(1)) // Only check last 24h
                ->get();

            if ($pendingTopups->count() > 0) {
                 $gateway = \App\Models\PaymentGateway::where('gateway_name', 'Midtrans')
                    ->where('is_active', true)
                    ->first();
                 
                 if ($gateway) {
                     foreach ($pendingTopups as $payment) {
                         $this->checkPendingPaymentStatus($payment, $gateway);
                     }
                 }
            }
        } catch (\Exception $e) {
            Log::error("Auto-verify topup error: " . $e->getMessage());
        }

        $perPage = $request->input('per_page', 10);
        if ($perPage == 'all') $perPage = 1000;

        // Find payments that are topups (transaction starts with TOP-)
        $history = Payment::where('isp_id', $isp->id)
            ->where('transaction_id', 'like', 'TOP-%')
            ->orderBy('created_at', 'desc')
            ->paginate((int)$perPage);

        return response()->json($history);
    }

    /**
     * Update referral code
     */


    private function checkPendingPaymentStatus($payment, $paymentGateway)
    {
        $gatewayName = strtolower($paymentGateway->gateway_name);
        
        // Get Server Key
        $serverKey = $paymentGateway->secret_key 
            ?? ($paymentGateway->settings['server_key'] ?? null)
            ?? $paymentGateway->merchant_id;
            
        if ($gatewayName === 'midtrans' && $serverKey) {
            return $this->checkMidtransStatus($payment, $serverKey);
        }
        
        // Fallback to time-based check if gateway check not implemented or config missing
        if ($payment->expiry_time && now()->gt($payment->expiry_time)) {
            $payment->update(['status' => 'failed', 'notes' => 'Payment expired (Time check)']);
            return ['status' => 'expired'];
        }
        
        // Assume active if within time
        return ['status' => 'active'];
    }

    private function checkMidtransStatus($payment, $serverKey)
    {
        \Midtrans\Config::$serverKey = $serverKey;
        // Use gateway config for isProduction if available, otherwise default logic
        // We assume the caller passed the correct key for the environment
        \Midtrans\Config::$isProduction = !\Str::contains($serverKey, 'SB-'); 
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
        
        try {
            $status = \Midtrans\Transaction::status($payment->payment_reference);
            $transactionStatus = $status->transaction_status;
            
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                // Paid! Update DB
                // Check if already success to avoid double credit
                if ($payment->status !== 'success') {
                    $payment->update([
                        'status' => 'success', 
                        'payment_date' => now(),
                        'paid_at' => now(),
                        'gateway_response' => (array)$status,
                        'payment_method' => 'midtrans'
                    ]);
                    
                    // HANDLE TOPUP CREDIT
                    if (str_starts_with($payment->transaction_id, 'TOP-')) {
                         $isp = ISP::find($payment->isp_id);
                         if ($isp) {
                             $isp->increment('balance', (float)$payment->amount);
                             Log::info("Auto-verified Topup {$payment->transaction_id}. Credited {$payment->amount} to ISP {$isp->id}");
                         }
                    } 
                    // HANDLE INVOICE PAYMENT
                    elseif ($payment->invoice_id) {
                        $invoice = Invoice::find($payment->invoice_id);
                        if ($invoice) {
                            $invoice->update(['payment_status' => 'paid', 'paid_amount' => $payment->amount, 'paid_at' => now(), 'payment_method' => 'midtrans']);
                            $subscription = ISPOrder::find($invoice->subscription_id);
                            if ($subscription) {
                                $subscription->update(['status' => 'active', 'payment_status' => 'paid']);
                            }
                        }
                    }
                }
                return ['status' => 'success'];
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'])) {
                $payment->update(['status' => 'failed', 'notes' => 'Gateway Status: ' . $transactionStatus]);
                return ['status' => 'expired'];
            } elseif ($transactionStatus === 'pending') {
                return ['status' => 'active'];
            }
            
        } catch (\Exception $e) {
            if ($e->getCode() == 404 && now()->diffInMinutes($payment->created_at) < 60) {
                 return ['status' => 'active'];
            }
             // Don't fail immediately on connection error, just keep pending
             Log::error("Midtrans Check Error: " . $e->getMessage());
             return ['status' => 'active'];
        }
        
        return ['status' => 'active'];
    }

    // End of Helper Functions for Status Check

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
                // Pass invoice_id if available (it might be passed as 7th arg now)
                $invoiceId = func_num_args() > 7 ? func_get_arg(7) : null;
                return $this->createMidtransPayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order, $serverKey, $invoiceId);
            
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
    private function createMidtransPayment($isp, $package, $amount, $billingCycle, $paymentGateway, $order, $serverKey, $invoiceId = null)
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

        // Generate unique order ID for this transaction attempt
        // We use 'ORD-INV-{id}-{timestamp}' formatted but store it so we can reference it
        // However, if we want consistency, we should check if one exists, but the caller handles that.
        // Here we create a NEW one because caller decided to create new.
        
        $transactionId = 'ORD-' . ($invoiceId ? 'INV-' . $invoiceId : 'PKG-' . $package->id) . '-' . time();
        
        // Create Payment Record (Pending)
        $payment = Payment::create([
            'isp_id' => $isp->id,
            'invoice_id' => $invoiceId,
            'payment_reference' => $transactionId,
            'amount' => $amount,
            'payment_method' => 'midtrans', // Initial method
            'status' => 'pending',
            'payment_date' => now(),
            'notes' => 'Generated via ' . $paymentGateway->gateway_name,
        ]);

        // Calculate expiry duration in hours (Midtrans uses hours or minutes, but we'll stick to hours for simplicity or convert accordingly)
        // Default to 24 hours if not set
        $expiryMinutes = $paymentGateway->expiry_duration ?: 1440;
        $expiryHours = ceil($expiryMinutes / 60);

        $params = [
            'transaction_details' => [
                'order_id' => $transactionId,
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
                    'name' => 'Package: ' . $package->name,
                ]
            ],
            'callbacks' => [
                'finish' => config('app.frontend_url', 'http://localhost:8080') . '/payment-callback?status=success&order_id=' . $transactionId,
                'error' => config('app.frontend_url', 'http://localhost:8080') . '/payment-callback?status=failed',
            ],
            'expiry' => [
                'start_time' => date("Y-m-d H:i:s O"),
                'unit' => 'minutes',
                'duration' => $expiryMinutes
            ]
        ];

        try {
            $snapResponse = \Midtrans\Snap::createTransaction($params);
            
            // Update payment with redirect URL and expiry time
            $payment->update([
                'payment_details' => [
                    'redirect_url' => $snapResponse->redirect_url,
                    'token' => $snapResponse->token
                ],
                'expiry_time' => now()->addMinutes($expiryMinutes)
            ]);
            
            return [
                'redirect_url' => $snapResponse->redirect_url,
                'token' => $snapResponse->token
            ];
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed', 'notes' => 'Failed: ' . $e->getMessage()]);
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

    /**
     * Download invoice as PDF
     */
    public function downloadInvoice($id)
    {
        try {
            $user = auth()->user();
            $isp = $user->isp;

            $invoice = Invoice::with(['items', 'payment'])
                ->where('billable_type', 'App\\Models\\ISP')
                ->where('billable_id', $isp->id)
                ->where('id', $id)
                ->firstOrFail();

            // Generate PDF using dompdf
            $pdf = Pdf::loadView('invoices.pdf', [
                'invoice' => $invoice,
                'isp' => $isp
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            // Download PDF
            return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download invoice: ' . $e->getMessage(),
            ], 500);
        }
    }
}
