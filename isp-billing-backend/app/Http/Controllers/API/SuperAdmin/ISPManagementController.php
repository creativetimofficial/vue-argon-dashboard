<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use Illuminate\Http\Request;

class ISPManagementController extends Controller
{
    /**
     * Update the specified ISP in storage.
     */
    public function update(Request $request, $id)
    {
        $isp = ISP::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'company_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:isps,email,' . $isp->id,
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
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:isps,email',
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
        ]);

        $isp = ISP::create($validated);

        return response()->json([
            'message' => 'ISP created successfully',
            'isp' => $isp
        ], 201);
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
