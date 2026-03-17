<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\User;
use App\Models\SubscriptionPackage;
use App\Models\ISPOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ISPManagementController extends Controller
{
    /**
     * Update the specified ISP in storage.
     */
    public function update(Request $request, $id)
    {
        $isp = ISP::findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'logo' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'package_id' => 'nullable|integer',
            'subscription_package_id' => 'nullable|integer|exists:subscription_packages,id',
            'subscription_status' => 'nullable|string|in:trial,active,suspended,cancelled,expired',
            'subscription_start_date' => 'nullable|date',
            'subscription_end_date' => 'nullable|date',
            'trial_end_date' => 'nullable|date',
            'tax_id' => 'nullable|string|max:100',
            'business_license' => 'nullable|string|max:255',
            'is_verified' => 'nullable|boolean',
            'verified_at' => 'nullable|date',
            'current_customers_count' => 'nullable|integer',
            'current_users_count' => 'nullable|integer',
            'current_locations_count' => 'nullable|integer',
            'approval_status' => 'nullable|string|in:pending,approved,rejected',
            'approved_at' => 'nullable|date',
            'approved_by' => 'nullable|integer',
            'rejection_reason' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'subdomain' => 'nullable|string|max:50',
            'custom_domain' => 'nullable|string|max:255',
        ]);

        $isp->update($validated);

        return response()->json([
            'message' => 'ISP updated successfully',
            'isp' => $isp
        ]);
    }

    /**
     * Remove the specified ISP from storage.
     */
    public function destroy($id)
    {
        $isp = ISP::findOrFail($id);
        $isp->delete();
        return response()->json([
            'message' => 'ISP deleted successfully'
        ]);
    }
    /**
     * Store a newly created ISP in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'subdomain' => 'nullable|string|unique:isps,subdomain|max:50|required_without:custom_domain',
            'custom_domain' => 'nullable|string|unique:isps,custom_domain|max:255|required_without:subdomain',
            'subscription_package_id' => 'required|exists:subscription_packages,id',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // 1. Get Subscription Package details
            $package = SubscriptionPackage::findOrFail($validated['subscription_package_id']);
            $isTrial = $package->price == 0 || ($package->trial_days ?? 0) > 0;
            $subscriptionStatus = $isTrial ? 'trial' : 'active';
            $activeDays = $package->active_days ?? ($package->trial_days ?? 30);

            // 2. Create ISP
            $isp = ISP::create([
                'company_name' => $validated['company_name'],
                'email' => $validated['email'],
                'subdomain' => $validated['subdomain'],
                'custom_domain' => $validated['custom_domain'] ?? null,
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'],
                'subscription_package_id' => $package->id,
                'subscription_status' => $subscriptionStatus,
                'approval_status' => 'approved', // Manual creation is pre-approved
                'subscription_start_date' => now(),
                'subscription_end_date' => now()->addDays($activeDays),
                'is_active' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            // 3. Create ISP Admin User
            $user = User::create([
                'name' => explode('@', $validated['email'])[0],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'isp_admin',
                'isp_id' => $isp->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Set owner
            $isp->update(['owner_id' => $user->id]);

            // 4. Create Administrative Order Record
            ISPOrder::create([
                'isp_id' => $isp->id,
                'subscription_package_id' => $package->id,
                'reference' => 'ADM-' . strtoupper(\Illuminate\Support\Str::random(10)),
                'order_type' => 'package',
                'service_name' => 'Package: ' . $package->name,
                'price' => $package->price,
                'billing_cycle' => $isp->billing_cycle ?? 'monthly',
                'status' => $subscriptionStatus,
                'payment_status' => $isTrial ? 'unpaid' : 'paid',
                'start_date' => now(),
                'expired_date' => now()->addDays($activeDays),
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'notes' => 'Created via Super Admin Manual ISP Addition',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'ISP and Admin account created successfully',
                'isp' => $isp->load('subscriptionPackage')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create ISP: ' . $e->getMessage()
            ], 500);
        }
    }
    public function index(Request $request)
    {
        try {
            $query = ISP::with(['subscriptionPackage', 'users']);
            if ($request->has('approval_status')) {
                $query->where('approval_status', $request->approval_status);
            }
            $isps = $query->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            // Jika gagal eager loading, ambil data ISP tanpa relasi
            $query = ISP::query();
            if ($request->has('approval_status')) {
                $query->where('approval_status', $request->approval_status);
            }
            $isps = $query->orderBy('created_at', 'desc')->get();
        }
        return response()->json($isps);
    }

    public function show($id)
    {
        $isp = ISP::with(['subscriptionPackage', 'users', 'orders.subscriptionPackage'])->findOrFail($id);
        return response()->json($isp);
    }

    public function approve($id)
    {
        $isp = ISP::findOrFail($id);

        // If ISP has a trial package, activate it
        if ($isp->subscription_package_id) {
            $package = $isp->subscriptionPackage;
            $isTrial = $package && ($package->price == 0 || ($package->trial_days ?? 0) > 0);
            
            if ($isTrial) {
                // Set trial period
                $trialDays = $package->trial_days ?? 14; // Default 14 days
                $startDate = now();
                $endDate = $startDate->copy()->addDays($trialDays);
                
                $isp->update([
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'subscription_status' => 'trial',
                    'subscription_start_date' => $startDate,
                    'subscription_end_date' => $endDate,
                    'is_active' => true,
                ]);
                
                // Activate users
                $isp->users()->update(['is_active' => true]);
            } else {
                $isp->update([
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'is_active' => true,
                ]);
            }
        } else {
            $isp->update([
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'is_active' => true,
            ]);
        }

        return response()->json([
            'message' => 'ISP approved successfully',
            'isp' => $isp->load('subscriptionPackage')
        ]);
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $isp = ISP::findOrFail($id);

        $isp->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return response()->json([
            'message' => 'ISP rejected successfully',
            'isp' => $isp
        ]);
    }

    public function updateSubscription(Request $request, $id)
    {
        $validated = $request->validate([
            'subscription_package_id' => 'required|exists:subscription_packages,id',
            'subscription_status' => 'sometimes|in:trial,active,suspended,cancelled,expired',
        ]);

        $isp = ISP::findOrFail($id);
        $isp->update($validated);

        return response()->json([
            'message' => 'Subscription updated successfully',
            'isp' => $isp->load('subscriptionPackage')
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => ISP::count(),
            'pending' => ISP::where('approval_status', 'pending')->count(),
            'approved' => ISP::where('approval_status', 'approved')->count(),
            'active' => ISP::where('subscription_status', 'active')->count(),
        ];

        return response()->json($stats);
    }
}
