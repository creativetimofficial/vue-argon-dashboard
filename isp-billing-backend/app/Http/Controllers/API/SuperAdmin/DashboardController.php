<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        try {
            // Count total ISPs
            $totalIsps = ISP::count();

            // Count total customers across all ISPs
            $totalCustomers = User::where('role', 'customer')->count();

            // Count pending ISP approvals
            $pendingApprovals = ISP::where('approval_status', 'pending')->count();

            // Calculate total revenue (sum of all ISPs subscription prices)
            $totalRevenue = ISP::join('subscription_packages', 'isps.subscription_package_id', '=', 'subscription_packages.id')
                ->where('isps.subscription_status', 'active')
                ->sum('subscription_packages.price_monthly');

            // Count active users (users who logged in today)
            $activeUsers = User::whereDate('updated_at', today())->count();
            if ($activeUsers === 0) {
                $activeUsers = 1; // At least the current user
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_isps' => $totalIsps,
                    'total_customers' => $totalCustomers,
                    'total_revenue' => $totalRevenue,
                    'pending_approvals' => $pendingApprovals,
                    'active_users' => $activeUsers,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recentIsps(Request $request)
    {
        try {
            $isps = ISP::with(['subscriptionPackage:id,name'])
                ->select('isps.*', DB::raw('(SELECT COUNT(*) FROM users WHERE users.isp_id = isps.id AND users.role = "customer") as customers_count'))
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($isp) {
                    return [
                        'id' => $isp->id,
                        'name' => $isp->company_name,
                        'email' => $isp->email,
                        'subscription_status' => $isp->subscription_status,
                        'approval_status' => $isp->approval_status,
                        'customers_count' => $isp->customers_count,
                        'package_name' => $isp->subscriptionPackage->name ?? 'N/A',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $isps
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ISPs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
