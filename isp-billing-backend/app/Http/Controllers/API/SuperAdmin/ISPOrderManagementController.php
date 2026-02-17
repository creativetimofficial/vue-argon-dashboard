<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISPOrder;
use App\Models\ISPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ISPOrderManagementController extends Controller
{
    /**
     * Get all orders (for approval)
     */
    public function index(Request $request)
    {
        $query = ISPOrder::with(['isp', 'service', 'subscriptionPackage']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }

    /**
     * Approve order (especially for trial packages)
     */
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $order = ISPOrder::with(['service', 'subscriptionPackage', 'isp'])->findOrFail($id);

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is not pending approval'
                ], 400);
            }

            if ($order->service_id) {
                // Handle Service Order
                $service = $order->service;
                $isTrial = $service->trial_days > 0 || $service->price == 0;
                $billingCycle = $order->billing_cycle ?: 'monthly';

                // Calculate period
                $startDate = now();
                $totalDays = $isTrial ? ($service->trial_days ?: 7) : 30;
                
                if (!$isTrial) {
                    if ($billingCycle === 'quarterly') $totalDays = 90;
                    elseif ($billingCycle === 'semi_annual') $totalDays = 180;
                    elseif ($billingCycle === 'annual') $totalDays = 365;
                }
                
                $endDate = $totalDays ? $startDate->copy()->addDays($totalDays) : null;

                // Generate service credentials
                $username = date('YmdHis') . rand(1000, 9999);
                $password = Str::random(10);

                // Generate configs
                $serverAddress = 'binetsg1.perwiramedia.com'; // TODO: Get from service config
                $l2tpConfig = "/interface l2tp-client add name={$username} user={$username} password={$password} connect-to={$serverAddress} disabled=no";
                $sstpConfig = "/interface sstp-client add name={$username} user={$username} password={$password} connect-to={$serverAddress} disabled=no";

                $order->update([
                    'status' => 'active', 
                    'payment_status' => $isTrial ? 'paid' : $order->payment_status,
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'start_date' => $startDate,
                    'expired_date' => $endDate,
                    'username' => $username,
                    'password' => $password,
                    'server_address' => $serverAddress,
                    'l2tp_config' => $l2tpConfig,
                    'sstp_config' => $sstpConfig,
                ]);
            } elseif ($order->subscription_package_id) {
                // Handle Package Order
                $package = $order->subscriptionPackage;
                $isp = $order->isp;
                $billingCycle = $order->billing_cycle ?: 'monthly';

                // Calculate days
                $totalDays = $package->active_days; // Nullable

                $isTrial = $package->slug === 'trial' || ($package->price == 0);

                if ($isTrial) {
                    // TRIAL PACKAGE: Auto-activate and mark as paid
                    // Update ISP subscription
                    $isp->update([
                        'subscription_package_id' => $package->id,
                        'subscription_status' => 'trial',
                        'subscription_start_date' => now(),
                        'subscription_end_date' => $totalDays ? now()->addDays($totalDays) : null,
                        'is_active' => true,
                    ]);

                    $order->update([
                        'status' => 'active',
                        'payment_status' => 'paid',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                        'start_date' => now(),
                        'expired_date' => $totalDays ? now()->addDays($totalDays) : null,
                    ]);
                } else {
                    // PAID PACKAGE: Create invoice and generate payment URL
                    $order->update([
                        'status' => 'pending_payment', // Keep pending_payment until payment
                        'payment_status' => 'unpaid',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]);

                    // Create Invoice NOW (after approval)
                    $invoice = \App\Models\Invoice::create([
                        'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5)),
                        'billable_type' => 'App\\Models\\ISP',
                        'billable_id' => $isp->id,
                        'isp_id' => $isp->id,
                        'subscription_id' => $order->id,
                        'subtotal' => $order->price,
                        'tax' => 0,
                        'discount' => 0,
                        'total' => $order->price,
                        'payment_status' => 'unpaid',
                        'issue_date' => now(),
                        'due_date' => now()->addDays(3),
                        'payment_method' => null,
                        'notes' => 'Generated after approval for order ' . $order->reference
                    ]);

                    // Create Invoice Item
                    \App\Models\InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => "Package: " . $package->name,
                        'quantity' => 1,
                        'unit_price' => $order->price,
                        'total' => $order->price,
                    ]);

                    // Generate payment URL
                    $paymentGateway = \App\Models\PaymentGateway::where('is_active', true)
                        ->orderBy('is_default', 'desc')
                        ->first();

                    if ($paymentGateway) {
                        $transactionId = 'ORD-' . $order->id . '-' . time();
                        
                        // Create Payment Record (Pending)
                        \App\Models\Payment::create([
                            'isp_id' => $isp->id,
                            'invoice_id' => $invoice->id,
                            'transaction_id' => $transactionId,
                            'payment_reference' => $transactionId,
                            'amount' => $order->price,
                            'payment_method' => $paymentGateway->gateway_name,
                            'payment_gateway' => $paymentGateway->gateway_name,
                            'status' => 'pending',
                            'payment_date' => now(),
                            'notes' => 'Payment for approved order ' . $order->reference
                        ]);

                        // Use Factory to generate payment URL
                        try {
                            $gatewayService = \App\Factories\PaymentGatewayFactory::create($paymentGateway->gateway_name);
                            
                            $result = $gatewayService->createInvoice(
                                $transactionId,
                                $order->price,
                                "Payment for Package: " . $package->name,
                                $isp->email ?? $isp->user->email,
                                $isp->company_name ?? $isp->user->name
                            );

                            if ($result['success']) {
                                $paymentUrl = $result['payment_url'];
                                // Store payment URL in order notes or return in response
                                $order->update(['notes' => ($order->notes ?? '') . "\nPayment URL: " . $paymentUrl]);
                            }
                        } catch (\Exception $e) {
                            \Log::error('Payment gateway error during approval: ' . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            // Determine if this was a trial or paid package
            $isTrial = false;
            $paymentUrl = null;
            if ($order->subscription_package_id) {
                $package = $order->subscriptionPackage;
                $isTrial = $package->slug === 'trial' || ($package->price == 0);
                
                // Extract payment URL from notes if it exists
                if (!$isTrial && $order->notes) {
                    preg_match('/Payment URL: (.+)/', $order->notes, $matches);
                    $paymentUrl = $matches[1] ?? null;
                }
            } elseif ($order->service_id) {
                $service = $order->service;
                $isTrial = $service->trial_days > 0 || $service->price == 0;
            }

            return response()->json([
                'success' => true,
                'message' => $isTrial ? 'Order approved and activated successfully' : 'Order approved. Awaiting payment.',
                'order' => $order->load(['isp', 'service', 'subscriptionPackage']),
                'payment_url' => $paymentUrl,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approve order error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject order
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $order = ISPOrder::findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order is not pending'
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => ($order->notes ? $order->notes . "\n" : '') . 'Rejected: ' . ($validated['reason'] ?? 'No reason provided'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order rejected successfully',
        ]);
    }

    /**
     * Get order detail
     */
    public function show($id)
    {
        $order = ISPOrder::with(['isp', 'service', 'subscriptionPackage', 'approvedBy'])->findOrFail($id);
        return response()->json($order);
    }
}
