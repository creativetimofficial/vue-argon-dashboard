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

                if ($isTrial) {
                    // Calculate trial period
                    $startDate = now();
                    $endDate = $startDate->copy()->addDays($service->trial_days);

                    // Generate service credentials
                    $username = date('YmdHis') . rand(1000, 9999);
                    $password = Str::random(10);

                    // Generate configs
                    $serverAddress = 'binetsg1.perwiramedia.com'; // TODO: Get from service config
                    $l2tpConfig = "/interface l2tp-client add name={$username} user={$username} password={$password} connect-to={$serverAddress} disabled=no";
                    $sstpConfig = "/interface sstp-client add name={$username} user={$username} password={$password} connect-to={$serverAddress} disabled=no";

                    $order->update([
                        'status' => 'active',
                        'payment_status' => 'paid',
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
                } else {
                    // For paid services, just approve (payment handled separately)
                    $order->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]);
                }
            } elseif ($order->subscription_package_id) {
                // Handle Package Order
                $package = $order->subscriptionPackage;
                $isp = $order->isp;

                // Update ISP subscription
                $isp->update([
                    'subscription_package_id' => $package->id,
                    'subscription_status' => 'active',
                    'subscription_start_date' => now(),
                    'subscription_end_date' => now()->addMonth(), // Default to 1 month for now
                    'is_active' => true,
                ]);

                $order->update([
                    'status' => 'active',
                    'payment_status' => 'paid',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'start_date' => now(),
                    'expired_date' => now()->addMonth(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order approved successfully',
                'order' => $order->load(['isp', 'service']),
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
