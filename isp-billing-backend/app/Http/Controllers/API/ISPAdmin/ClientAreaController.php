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
        $isps = $user->ownedIsps;

        if ($isps->isEmpty()) {
            return response()->json([
                'balance' => 0,
                'active_services' => 0,
                'unpaid_invoices' => 0,
                'pending_orders' => 0,
            ]);
        }

        $ispIds = $isps->pluck('id');
        
        $balance = $isps->sum('balance');
        $activeServices = ISPOrder::whereIn('isp_id', $ispIds)
            ->where('status', 'active')
            ->count();
        $unpaidInvoices = Invoice::whereIn('isp_id', $ispIds)
            ->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->count();
        $pendingOrders = ISPOrder::whereIn('isp_id', $ispIds)
            ->where('status', 'pending')
            ->count();

        // Get "active" subscription if any (for UI display)
        $primaryIsp = $user->isp ?: $isps->first();

        return response()->json([
            'balance' => $balance,
            'active_services' => $activeServices,
            'unpaid_invoices' => $unpaidInvoices,
            'pending_orders' => $pendingOrders,
            'isp' => [
                'subscription_status' => $primaryIsp->subscription_status ?? 'cancelled',
                'subscription_end_date' => $primaryIsp->subscription_end_date ?? null,
                'package_name' => $primaryIsp->subscriptionPackage?->name ?? 'No Package',
                'package' => $primaryIsp->subscriptionPackage ?? null,
                'is_active' => $primaryIsp->is_active ?? false,
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
        $ispIds = $user->ownedIsps()->pluck('id');

        if ($ispIds->isEmpty()) {
            return response()->json([]);
        }

        $orders = ISPOrder::whereIn('isp_id', $ispIds)
            ->where('status', '!=', 'superseded')
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
        $ispIds = $user->ownedIsps()->pluck('id');

        $order = ISPOrder::whereIn('isp_id', $ispIds)
            ->where('id', $id)
            ->whereIn('status', ['pending_payment', 'expired', 'failed']) // Allow cancel for these statuses
            ->with('isp')
            ->firstOrFail();

        $isp = $order->isp;
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
        $ispIds = $user->ownedIsps()->pluck('id');

        $order = ISPOrder::whereIn('isp_id', $ispIds)
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
        $isp = $user->isp; // Keep as default, but don't fail if null

        // Validate Request
        $validated = $request->validate([
            'is_new_unit' => 'nullable|boolean',
            'isp_id' => 'nullable|exists:isps,id',
            'company_name' => 'required_if:is_new_unit,true|nullable|string|max:100',
            'server_id' => 'required_if:is_new_unit,true|nullable|exists:servers,id',
            'service_id' => 'nullable|exists:isp_services,id',
            'package_id' => 'nullable|exists:subscription_packages,id', // Fallback
            'billing_cycle' => 'nullable|string',
            'domain_type' => 'required_if:is_new_unit,true|nullable|in:subdomain,custom',
            'subdomain' => 'nullable|string|min:3|max:50',
            'domain' => 'nullable|string|min:4|max:100',
            'notes' => 'nullable|string|max:500',
            'referral_code' => 'nullable|string|exists:isps,referral_code',
        ]);

        // Determine which ISP to use or create
        $targetIsp = null;
        if (!empty($validated['is_new_unit'])) {
            // Check Company Name Uniqueness for current owner
            $existingName = \App\Models\ISP::where('owner_id', $user->id)
                ->where('company_name', $validated['company_name'])
                ->exists();
            if ($existingName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki unit ISP dengan nama "' . $validated['company_name'] . '". Silakan gunakan nama lain untuk unit baru ini.'
                ], 422);
            }
        } else {
            // Use Existing ISP Unit
            $targetIspId = $validated['isp_id'] ?? $user->isp_id; // Default to primary if not specified
            if (!$targetIspId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select an ISP unit or create a new one.'
                ], 422);
            }
            
            $targetIsp = \App\Models\ISP::where('id', $targetIspId)
                ->where('owner_id', $user->id)
                ->first();
                
            if (!$targetIsp) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Selected ISP unit not found or access denied.'
                ], 404);
            }

            // --- REDUNDANCY REFINEMENT ---
            // Inherit server from previous order if not provided
            if (empty($validated['server_id'])) {
                $lastOrder = ISPOrder::where('isp_id', $targetIsp->id)
                    ->whereNotNull('server_id')
                    ->latest()
                    ->first();
                
                if ($lastOrder) {
                    $validated['server_id'] = $lastOrder->server_id;
                } else {
                    // Fallback: Pick an available server if no history found for this unit
                    $fallbackServer = \App\Models\Server::where('is_active', true)
                        ->whereRaw('current_users < capacity')
                        ->orderBy('current_users', 'asc') // Pick least loaded
                        ->first();
                    
                    if ($fallbackServer) {
                        $validated['server_id'] = $fallbackServer->id;
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Tidak dapat menemukan server yang tersedia saat ini. Silakan hubungi admin.'
                        ], 422);
                    }
                }
            }

            // Inherit domain settings
            if (empty($validated['domain_type'])) {
                if ($targetIsp->custom_domain) {
                    $validated['domain_type'] = 'custom';
                    $validated['domain'] = $targetIsp->custom_domain;
                } else {
                    $validated['domain_type'] = 'subdomain';
                    $validated['subdomain'] = $targetIsp->subdomain;
                }
            }

            // --- CREDENTIAL INHERITANCE ---
            // Inherit VPN credentials from latest order for this ISP
            $inheritedOrder = ISPOrder::where('isp_id', $targetIsp->id)
                ->whereNotNull('username')
                ->whereNotNull('ip_address')
                ->latest()
                ->first();
            
            if ($inheritedOrder) {
                $validated['inherited_username'] = $inheritedOrder->username;
                $validated['inherited_password'] = $inheritedOrder->password;
                $validated['inherited_ip'] = $inheritedOrder->ip_address;
                $validated['inherited_server_address'] = $inheritedOrder->server_address;
                $validated['inherited_l2tp'] = $inheritedOrder->l2tp_config;
                $validated['inherited_sstp'] = $inheritedOrder->sstp_config;
            }
        }

        // Validate Referral Code (Only for New ISP Units)
        $referrerId = null;
        $discount = 0;
        if (!empty($validated['is_new_unit']) && !empty($validated['referral_code'])) {
            $referrer = \App\Models\ISP::where('referral_code', $validated['referral_code'])->first();
            
            // Allow referral if referrer exists and is NOT owned by the same user
            if ($referrer && $referrer->owner_id !== $user->id) {
                $referrerId = $referrer->id;
                $discount = 0.10; // 10% Discount
            } elseif ($referrer && $referrer->owner_id === $user->id) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menggunakan kode referal dari unit ISP Anda sendiri.'
                ], 422);
            }
        }

        // Determine Package/Service and handle validations
        $serviceId = $validated['service_id'] ?? null;
        $packageId = $validated['package_id'] ?? null;
        $package = null;
        $service = null;

        if ($packageId) {
            $package = \App\Models\SubscriptionPackage::findOrFail($packageId);
            $serviceName = $package->name;
            $price = $package->price;
            $requiresApproval = $package->requires_manual_approval;
            $isTrial = $price == 0 || $package->slug === 'trial';
        } elseif ($serviceId) {
            $service = ISPService::findOrFail($serviceId);
            $serviceName = $service->name;
            $price = $service->price;
            $requiresApproval = $service->requires_manual_approval;
            $isTrial = $service->trial_days > 0 || $service->price == 0;
        } else {
            return response()->json(['success' => false, 'message' => 'Please select a service or package.'], 422);
        }

        // --- VALIDATIONS ---
        if ($targetIsp) {
            // 1. Block Trials for existing units (Renewals/Upgrades)
            if ($isTrial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket Trial hanya tersedia untuk unit ISP baru. Untuk unit yang sudah terdaftar, silakan pilih paket berbayar.'
                ], 400);
            }

            // 2. Capacity Check for Downgrades
            if ($package) {
                $currentCustomerCount = \App\Models\Customer::where('isp_id', $targetIsp->id)->count();
                if ($package->max_customers > 0 && $currentCustomerCount > $package->max_customers) {
                    return response()->json([
                        'success' => false,
                        'message' => "Gagal mengubah paket. Jumlah pelanggan Anda saat ini ({$currentCustomerCount}) melebihi kapasitas maksimal paket {$package->name} ({$package->max_customers})."
                    ], 422);
                }
            }
        }

        // Custom Validation: Uniqueness for Active Orders/ISPs (Only for new units/changes)
        if (empty($validated['isp_id'])) {
            if ($validated['domain_type'] === 'subdomain' && !empty($validated['subdomain'])) {
                $existsInOrders = \App\Models\ISPOrder::where('subdomain', $validated['subdomain'])
                    ->whereIn('status', ['active', 'pending_payment', 'paid'])
                    ->exists();
                $existsInIsps = \App\Models\ISP::where('subdomain', $validated['subdomain'])->exists();
                
                if ($existsInOrders || $existsInIsps) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Subdomain "' . $validated['subdomain'] . '" is already taken or processing.'
                    ], 422);
                }
            }
            if ($validated['domain_type'] === 'custom' && !empty($validated['domain'])) {
                 $existsInOrders = \App\Models\ISPOrder::where('domain', $validated['domain'])
                    ->whereIn('status', ['active', 'pending_payment', 'paid'])
                    ->exists();
                 $existsInIsps = \App\Models\ISP::where('custom_domain', $validated['domain'])->exists();
                    
                if ($existsInOrders || $existsInIsps) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Domain "' . $validated['domain'] . '" is already registered or processing.'
                    ], 422);
                }
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

            // Create New ISP Unit if requested
            if (!empty($validated['is_new_unit'])) {
                $targetIsp = \App\Models\ISP::create([
                    'owner_id' => $user->id,
                    'company_name' => $validated['company_name'],
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'whatsapp' => $user->phone,
                    'referral_code' => Str::upper(Str::random(5)),
                    'subdomain' => $validated['domain_type'] === 'subdomain' ? $validated['subdomain'] : null,
                    'custom_domain' => $validated['domain_type'] === 'custom' ? $validated['domain'] : null,
                    'is_active' => false,
                    'approval_status' => 'pending'
                ]);
                
                // Set as primary if user has none
                if (!$user->isp_id) {
                    $user->update(['isp_id' => $targetIsp->id]);
                }
            }
            
            $isp = $targetIsp; 
            $billingCycle = $validated['billing_cycle'] ?? 'monthly';
            $paymentStatus = 'unpaid';

            // Apply Referral Discount
            if ($discount > 0 && !$isTrial) {
                $price = $price - ($price * $discount);
            }

            // Generate reference
            $reference = 'order-' . date('YmdHis') . rand(1000, 9999);

            // Generate domain
            $domain = null;
            if ($validated['domain_type'] === 'subdomain') {
                $mainDomainSetting = \App\Models\SystemSetting::get('main_domain', ['base_domain' => 'localhost']);
                $domain = ($validated['subdomain'] ?? $isp->subdomain) . '.' . ($mainDomainSetting['base_domain'] ?? 'localhost');
            } else {
                $domain = $validated['domain'] ?? $isp->custom_domain;
            }

            $status = 'pending_payment';

            // Auto-approve if trial and not requiring manual approval
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
                'service_name' => $serviceName,
                'domain_type' => $validated['domain_type'],
                'subdomain' => $validated['subdomain'] ?? $isp->subdomain,
                'domain' => $domain,
                'notes' => $validated['notes'] ?? (($packageId) ? "Ordering Package: " . $package->name : null),
                'payment_expired_at' => $paymentExpiredAt,
                'payment_gateway' => $paymentGateway ? $paymentGateway->gateway_name : null,
                'domain_active' => false, // Will be activated after payment
                // Pass inherited credentials
                'username' => $validated['inherited_username'] ?? null,
                'password' => $validated['inherited_password'] ?? null,
                'ip_address' => $validated['inherited_ip'] ?? null,
                'server_address' => $validated['inherited_server_address'] ?? null,
                'l2tp_config' => $validated['inherited_l2tp'] ?? null,
                'sstp_config' => $validated['inherited_sstp'] ?? null,
            ]);

            // If auto-activated, generate credentials or update ISP subscription
            // Only provision if ACTIVE and (Trial OR Paid)
            if ($status === 'active' && ($isTrial || $paymentStatus === 'paid')) {
                if ($serviceId || $packageId) {
                    $username = date('YmdHis') . rand(100, 999);
                    // Centralized generation in model
                    $order->generateCredentials();
                    
                    $order->generateCredentials();
                    
                    // Unified Activation (Handles Fair Extension, ISP update, and Consolidation)
                    $order->activateSubscription();
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
        $isps = $user->ownedIsps()->pluck('id');

        $orders = ISPOrder::whereIn('isp_id', $isps)
            ->whereIn('status', ['active', 'pending', 'approved'])
            ->with(['isp', 'service', 'subscriptionPackage'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Get all ISPs owned by the user
     */
    public function ownedIsps()
    {
        $isps = auth()->user()->ownedIsps()
            ->with(['subscriptionPackage'])
            ->get();

        return response()->json($isps);
    }

    /**
     * Get service detail
     */
    public function serviceDetail($id)
    {
        $user = auth()->user();
        $ispIds = $user->ownedIsps()->pluck('id');

        $order = ISPOrder::whereIn('isp_id', $ispIds)
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
        $ispIds = $user->ownedIsps()->pluck('id');

        $order = ISPOrder::whereIn('isp_id', $ispIds)
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
        $ispIds = $user->ownedIsps()->pluck('id');

        if ($ispIds->isEmpty()) {
            return response()->json([]);
        }

        $invoices = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->whereIn('billable_id', $ispIds)
            ->with(['payment', 'billable'])
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
        $ispIds = $user->ownedIsps()->pluck('id');

        $invoice = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->whereIn('billable_id', $ispIds)
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
        $ispIds = $user->ownedIsps()->pluck('id');

        $invoice = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->whereIn('billable_id', $ispIds)
            ->where('id', $id)
            ->where('payment_status', 'unpaid')
            ->with('billable')
            ->firstOrFail();

        $isp = $invoice->billable;

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
        $ispIds = $user->ownedIsps()->pluck('id');

        $invoice = Invoice::where('billable_type', 'App\\Models\\ISP')
            ->whereIn('billable_id', $ispIds)
            ->where('id', $id)
            ->where('payment_status', 'unpaid')
            ->with('billable')
            ->firstOrFail();

        $isp = $invoice->billable;

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
                            // Centralized Activation and Provisioning (calls activateSubscription internally)
                            $order->activateDomain();

                            // Send Active Email
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
            'amount' => 'required|numeric|min:1000000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        if ($isp->balance < $validated['amount']) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        // --- SECURITY GUARDRAILS --- LIMIT HARIAN 2 KALI
        $today = \Carbon\Carbon::today();
        
        // 1. Frequency Limit: Max 2 withdrawals per day
        $withdrawalsTodayCount = \App\Models\Withdrawal::where('isp_id', $isp->id)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'rejected') // Don't count failed ones as limit
            ->count();
            
        if ($withdrawalsTodayCount >= 2) {
             return response()->json(['message' => 'Limit harian tercapai. Anda hanya dapat melakukan maksimal 2 kali penarikan dalam sehari.'], 400);
        }

        // 2. Amount Limit: Max Rp 50.000.000 per day
        $withdrawalsTodayAmount = \App\Models\Withdrawal::where('isp_id', $isp->id)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'rejected')
            ->sum('amount');
            
        $dailyLimit = 50000000;
        if (($withdrawalsTodayAmount + $validated['amount']) > $dailyLimit) {
             return response()->json(['message' => 'Total penarikan melampaui limit harian (Maks. Rp 50.000.000/hari). Sisa limit Anda hari ini: Rp ' . number_format($dailyLimit - $withdrawalsTodayAmount, 0, ',', '.')], 400);
        }
        // --- END SECURITY GUARDRAILS ---

        try {
            DB::beginTransaction();

            $adminFee = 5000; // Flat fee for Xendit disbursement
            $totalTransfer = $validated['amount'] - $adminFee;

            if ($totalTransfer <= 0) {
                 return response()->json(['message' => 'Withdrawal amount must be greater than the admin fee (Rp 5.000)'], 400);
            }

            // Deduct balance immediately
            $isp->decrement('balance', $validated['amount']);

            // Create pending withdrawal
            $withdrawal = \App\Models\Withdrawal::create([
                'isp_id' => $isp->id,
                'amount' => $validated['amount'],
                'admin_fee' => $adminFee,
                'total_transfer' => $totalTransfer,
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
                'status' => 'pending', // Directly use pending for Xendit processing
                'notes' => 'Withdrawal request processing via Xendit'
            ]);

            // Attempt to disburse via Xendit
            $xenditResult = $this->createXenditDisbursement($withdrawal, $isp);

            if ($xenditResult['success']) {
                $xData = $xenditResult['data'];
                $wUpdate = [
                    'xendit_disbursement_id' => $xData['id'],
                ];

                // Check for immediate status or FORCED FAILURE for magic numbers in Sandbox
                $isSandboxFailure = (config('app.env') !== 'production' && ($withdrawal->account_number === '0000000000' || $withdrawal->account_number === '9999999999'));

                if ($isSandboxFailure || (isset($xData['status']) && $xData['status'] === 'FAILED')) {
                    $reason = $isSandboxFailure ? 'Simulasi Gagal (Magic Number)' : ($xData['failure_code'] ?? 'Unknown');
                    $wUpdate['status'] = 'rejected';
                    $wUpdate['rejected_at'] = now();
                    $wUpdate['notes'] = 'Gagal (Gateway): ' . $reason;
                    
                    // Refund balance immediately
                    $isp->increment('balance', $withdrawal->amount);
                    Log::info("Withdrawal #{$withdrawal->id} marked as REJECTED instantly (Sandbox/Gateway Failure).");
                } elseif (isset($xData['status']) && $xData['status'] === 'COMPLETED') {
                    $wUpdate['status'] = 'approved';
                    $wUpdate['approved_at'] = now();
                    $wUpdate['notes'] = 'Berhasil (Instan dari Gateway)';
                }

                $withdrawal->update($wUpdate);
                
                // --- SANDBOX OPTIMIZATION (Polling) ---
                // Only poll if it was not forced to failure and is still pending
                if ($withdrawal->refresh()->status === 'pending') {
                    sleep(2);
                    
                    // Manually poll status from Xendit
                    $statusPoll = $this->getXenditDisbursementStatus($xData['id']);
                    if ($statusPoll['success'] && isset($statusPoll['data']['status'])) {
                        $sData = $statusPoll['data'];
                        $isSandboxFailurePoll = (config('app.env') !== 'production' && $withdrawal->account_number === '0000000000');
                        
                        if ($isSandboxFailurePoll || $sData['status'] === 'FAILED') {
                            $reason = $isSandboxFailurePoll ? 'Simulasi Gagal (Magic Number)' : ($sData['failure_code'] ?? 'Unknown');
                            $withdrawal->update([
                                'status' => 'rejected',
                                'rejected_at' => now(),
                                'notes' => 'Payout Gagal: ' . $reason
                            ]);
                            $isp->increment('balance', $withdrawal->amount);
                            Log::info("Withdrawal #{$withdrawal->id} marked as REJECTED after polling.");
                            
                            // Send failure email
                            $this->sendWithdrawalNotification($withdrawal, $isp);
                        } elseif ($sData['status'] === 'COMPLETED') {
                            $withdrawal->update([
                                'status' => 'approved',
                                'approved_at' => now(),
                                'notes' => 'Payout Berhasil (Verified via API Pool)'
                            ]);
                            Log::info("Withdrawal #{$withdrawal->id} marked as APPROVED after polling.");
                        }
                    }
                }
                // --- END SANDBOX OPTIMIZATION ---
            } else {
                 // Revert balance if Xendit API creation fails entirely
                 $isp->increment('balance', $validated['amount']);
                 $withdrawal->update([
                     'status' => 'rejected',
                     'notes' => 'Payout gateway error: ' . $xenditResult['message']
                 ]);
                 DB::commit(); // Commit the failed state record
                 
                 // Send failure email
                 $this->sendWithdrawalNotification($withdrawal, $isp);

                 return response()->json(['message' => 'Gagal memproses penarikan: ' . $xenditResult['message']], 500);
            }

            DB::commit();

            // Refresh model to get the latest status (Approved/Rejected) after polling
            $withdrawal->refresh();

            // Fire off Emails asynchronously
            try {
                $this->sendWithdrawalNotification($withdrawal, $isp);
            } catch (\Exception $e) {
                Log::error('Withdrawal Email Error: ' . $e->getMessage());
            }

            $finalStatus = $withdrawal->status;
            $msg = ($finalStatus === 'approved') 
                ? 'Penarikan BERHASIL diproses!' 
                : (($finalStatus === 'rejected') ? 'Penarikan GAGAL (Dana dikembalikan ke saldo).' : 'Permintaan penarikan sedang diproses');

            return response()->json([
                'success' => true,
                'message' => $msg,
                'status' => $finalStatus,
                'balance' => $isp->refresh()->balance
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Withdrawal failed: ' . $e->getMessage()], 500);
        }
    }

    private function createXenditDisbursement($withdrawal, $isp)
    {
        $paymentGateway = \App\Models\PaymentGateway::where('gateway_name', 'Xendit')->first();
        if (!$paymentGateway || !$paymentGateway->is_active) {
            return ['success' => false, 'message' => 'Xendit is not configured or active'];
        }

        $apiKey = $paymentGateway->secret_key ?? ($paymentGateway->settings['server_key'] ?? null);
        if (!$apiKey) {
            return ['success' => false, 'message' => 'Valid Xendit API Key not found'];
        }

        $accountNumber = $withdrawal->account_number;
        
        // Normalize phone numbers for E-Wallets
        $eWallets = ['DANA', 'GOPAY', 'OVO', 'LINKAJA', 'SHOPEEPAY'];
        if (in_array(strtoupper($withdrawal->bank_name), $eWallets)) {
            // Remove all non-numeric characters except '+'
            $accountNumber = preg_replace('/[^0-9+]/', '', $accountNumber);
            
            // Format +62 to 0
            if (strpos($accountNumber, '+62') === 0) {
                $accountNumber = '0' . substr($accountNumber, 3);
            } 
            // Format 62 to 0
            elseif (strpos($accountNumber, '62') === 0 && strlen($accountNumber) > 10) {
                 $accountNumber = '0' . substr($accountNumber, 2);
            }
        }

        $referenceId = 'WD-' . $withdrawal->id . '-' . time();
        $params = [
            'external_id' => $referenceId,
            'amount' => (int) $withdrawal->total_transfer,
            'bank_code' => strtoupper($withdrawal->bank_name), // Needs to match Xendit bank codes
            'account_holder_name' => $withdrawal->account_name,
            'account_number' => $accountNumber,
            'description' => 'Withdrawal for ISP ' . $isp->company_name,
        ];

        try {
            $ch = curl_init('https://api.xendit.co/disbursements');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($apiKey . ':')
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

            // For development: disable SSL verification if configured
            if (config('app.env') !== 'production') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $responseData = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                 return ['success' => true, 'data' => $responseData];
            } else {
                 Log::error('Xendit Disbursement Error: ' . $response);
                 return ['success' => false, 'message' => $responseData['message'] ?? ($responseData['error_code'] ?? 'Unknown gateway error')];
            }
        } catch (\Exception $e) {
             Log::error('Xendit Request Exception: ' . $e->getMessage());
             return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get Disbursement Status from Xendit
     */
    private function getXenditDisbursementStatus($disbursementId)
    {
        $gateway = \App\Models\PaymentGateway::where('gateway_name', 'Xendit')->first();
        $apiKey = $gateway ? $gateway->api_key : env('XENDIT_SECRET_KEY');

        try {
            $ch = curl_init('https://api.xendit.co/disbursements/' . $disbursementId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . base64_encode($apiKey . ':')
            ]);
            
            if (config('app.env') !== 'production') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $responseData = json_decode($response, true);
            \Log::info("Xendit Disbursement Status Poll for {$disbursementId} (HTTP {$httpCode}):", (array)$responseData);

            if ($httpCode >= 200 && $httpCode < 300) {
                 return ['success' => true, 'data' => $responseData];
            }
            return ['success' => false, 'message' => 'Status poll failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Verify Bank Account Name using Xendit
     */
    public function verifyBankAccount(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|string',
            'bank_account_number' => 'required|string',
        ]);

        $bankCode = $request->input('bank_code');
        $accountNumber = $request->input('bank_account_number');

        $paymentGateway = \App\Models\PaymentGateway::where('gateway_name', 'Xendit')->first();
        if (!$paymentGateway || !$paymentGateway->is_active) {
            return response()->json(['success' => false, 'message' => 'Xendit payment gateway is not active.'], 400);
        }

        $apiKey = $paymentGateway->secret_key ?? ($paymentGateway->settings['server_key'] ?? null);
        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'Xendit API Key not configured.'], 400);
        }

        // Xendit's Name Validator currently does not support E-Wallets (returns 404).
        // By-pass verification for E-Wallets and allow manual input.
        $eWallets = ['DANA', 'GOPAY', 'OVO', 'LINKAJA', 'SHOPEEPAY'];
        if (in_array(strtoupper($bankCode), $eWallets)) {
            return response()->json([
                'success' => true,
                'is_ewallet' => true,
                'message' => 'E-Wallet selected. Please input the name manually.',
                'account_name' => '' // Leave empty to unlock manual input on frontend
            ]);
        }

        try {
            $ch = curl_init('https://api.xendit.co/bank_account_data_requests');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($apiKey . ':')
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'bank_account_number' => $accountNumber,
                'bank_code' => $bankCode
            ]));

            if (config('app.env') !== 'production') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $responseData = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                 if (isset($responseData['status']) && $responseData['status'] === 'SUCCESS') {
                      return response()->json([
                          'success' => true,
                          'account_name' => $responseData['bank_account_name']
                      ]);
                 } else {
                      return response()->json([
                          'success' => false,
                          'message' => 'Rekening tidak ditemukan atau salah. Pastikan nomor rekening dan bank yang dipilih benar.'
                      ], 400);
                 }
            } else {
                 // Check if it's a 404 Not Found (meaning Iluma/Name Validator is not active on this Xendit account yet)
                 // Or if it's explicitly returning "NOT_FOUND" 
                 if ($httpCode === 404 || (isset($responseData['error_code']) && $responseData['error_code'] === 'NOT_FOUND')) {
                     Log::info('Xendit Name Validator is returning 404. Proceeding with Fallback bypass. Payload: ' . $response);
                     return response()->json([
                         'success' => true,
                         'is_fallback' => true,
                         'message' => 'Pengecekan otomatis tidak tersedia. Silakan masukkan nama pemilik rekening secara manual.',
                         'account_name' => '' // Unlock manual input
                     ]);
                 }

                 Log::error('Xendit Bank Verification Error: ' . $response);
                 $xenditError = $responseData['message'] ?? ($responseData['error_code'] ?? 'Gagal memverifikasi bank ke gateway.');
                 
                 return response()->json([
                     'success' => false,
                     'message' => 'Xendit Error: ' . $xenditError
                 ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Xendit Name Validator Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem saat verifikasi rekening.'], 500);
        }
    }

    /**
     * Handle Xendit Disbursement Webhook
     */
    public function xenditDisbursementCallback(Request $request)
    {
        Log::info('Xendit Disbursement Webhook Received:', $request->all());

        // --- WEBHOOK SECURITY TOKEN CHECK ---
        $xenditXCallbackToken = env('XENDIT_X_CALLBACK_TOKEN');
        $callbackTokenHeader = $request->header('X-CALLBACK-TOKEN');

        if ($xenditXCallbackToken && $callbackTokenHeader !== $xenditXCallbackToken) {
            Log::warning('Xendit Webhook Unauthorized: Invalid Callback Token.');
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // --- END SECURITY CHECK ---

        try {
            DB::beginTransaction();
            
            $disbursementId = $request->input('id');
            $status = $request->input('status'); // COMPLETED, FAILED
            $externalId = $request->input('external_id');
            $failureCode = $request->input('failure_code');

            if (!$disbursementId) {
                return response()->json(['message' => 'Invalid payload'], 400);
            }

            // Handle Xendit's "Test Webhook" dummy payload gracefully
            if ($disbursementId === '57e214ba82b034c325e84d6e' || $externalId === 'disbursement_123124123') {
                 Log::info("Xendit Test Webhook acknowledged.");
                 return response()->json(['success' => true, 'message' => 'Test webhook received']);
            }

            // Find the corresponding withdrawal
            $withdrawal = \App\Models\Withdrawal::where('xendit_disbursement_id', $disbursementId)->first();

            if (!$withdrawal) {
                Log::warning("Withdrawal not found for Xendit Disbursement ID: $disbursementId");
                return response()->json(['message' => 'Withdrawal record not found'], 404);
            }

            // Processing logic based on status
            // FORCED FAILURE FOR SANDBOX MAGIC NUMBERS to prevent accidental approval
            $isSandboxFailure = (config('app.env') !== 'production' && ($withdrawal->account_number === '0000000000' || $withdrawal->account_number === '9999999999'));

            if ($status === 'FAILED' || $isSandboxFailure) {
                if ($withdrawal->status !== 'rejected') {
                    $reason = $isSandboxFailure ? 'Simulasi Gagal (Magic Number)' : ($failureCode ?? 'Unknown error');
                    // Update status
                    $withdrawal->update([
                        'status' => 'rejected',
                        'rejected_at' => now(),
                        'notes' => 'Payout Gagal: ' . $reason
                    ]);
                    
                    // Refund the user balance
                    $isp = \App\Models\ISP::find($withdrawal->isp_id);
                    if ($isp) {
                        // Ensure we return the FULL gross amount to the user
                        $isp->increment('balance', $withdrawal->amount);
                        Log::info("Withdrawal #{$withdrawal->id} REJECTED (Webhook). Subtracted amount refunded to ISP #{$isp->id}.");
                    }
                    
                    // Send notification for status change
                    $this->sendWithdrawalNotification($withdrawal, $isp);
                }
            } elseif ($status === 'COMPLETED') {
                if ($withdrawal->status !== 'approved') {
                    $withdrawal->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'notes' => 'Payout completed successfully'
                    ]);
                    Log::info("Withdrawal #{$withdrawal->id} marked as APPROVED (Webhook).");
                    
                    // Send notification for status change
                    $isp = \App\Models\ISP::find($withdrawal->isp_id);
                    $this->sendWithdrawalNotification($withdrawal, $isp);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Xendit Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal server error processing webhook'], 500);
        }
    }

    /**
     * Helper to send Withdrawal Notifications
     */
    private function sendWithdrawalNotification($withdrawal, $isp)
    {
        if (!$isp) return;
        
        try {
            $withdrawal->refresh(); // Just to be safe, get latest state
            $user = $isp->owner; // Try to get actual owner if possible
            if (!$user) {
                // Fallback to searching by isp_id or use the user email associated with this session
                $user = \App\Models\User::where('isp_id', $isp->id)->first();
            }

            if ($user && $user->email) {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WithdrawalNotification($withdrawal, $isp, false));
            }
            
            // Email Laporan ke Super Admin (Owner)
            $adminEmail = env('ADMIN_EMAIL', 'admin@billing.local');
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\WithdrawalNotification($withdrawal, $isp, true));
        } catch (\Exception $e) {
            Log::error('Manual Withdrawal Notification Error: ' . $e->getMessage());
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
            $perPage = $request->input('per_page', 10);
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => (int)$perPage,
                'total' => 0
            ]);
        }

        $perPage = $request->input('per_page', 10);
        if ($perPage == 'all') $perPage = 1000;

        $history = \App\Models\Withdrawal::where('isp_id', $isp->id)
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
            $perPage = $request->input('per_page', 10);
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => (int)$perPage,
                'total' => 0
            ]);
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
                             
                             try {
                                 $ispUser = $isp->users()->first();
                                 if($ispUser) {
                                     \Illuminate\Support\Facades\Mail::to($ispUser->email)->send(new \App\Mail\TopupNotification($payment, $isp));
                                 }
                             } catch (\Exception $e) {
                                 Log::error("Failed to send Topup email: " . $e->getMessage());
                             }
                         }
                    } 
                    // HANDLE INVOICE PAYMENT
                    elseif ($payment->invoice_id) {
                        $invoice = Invoice::find($payment->invoice_id);
                        if ($invoice) {
                            $invoice->update(['payment_status' => 'paid', 'paid_amount' => $payment->amount, 'paid_at' => now(), 'payment_method' => 'midtrans']);
                            $subscription = ISPOrder::find($invoice->subscription_id);
                            if ($subscription) {
                                // Centralized Activation (Handles Fair Extension, ISP update, and Consolidation)
                                $subscription->activateSubscription();
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
