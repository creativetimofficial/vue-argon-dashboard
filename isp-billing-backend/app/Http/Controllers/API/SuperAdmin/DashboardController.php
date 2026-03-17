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
                ->sum('subscription_packages.price');

            // Count active users (users who logged in today)
            $activeUsers = User::whereDate('updated_at', today())->count();
            if ($activeUsers === 0) {
                $activeUsers = 1; // At least the current user
            }

            // Calculate monthly revenue for current year
            $monthlyRevenue = [];
            $currentYear = now()->year;
            
            // Initialize all months with 0
            for ($i = 1; $i <= 12; $i++) {
                $monthlyRevenue[$i] = 0;
            }

            // Get successful payments grouped by month
            $revenueData = \App\Models\Payment::whereYear('payment_date', $currentYear)
                ->where('status', 'success')
                ->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            // Fill in the data
            foreach ($revenueData as $month => $amount) {
                $monthlyRevenue[$month] = (int) $amount;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_isps' => $totalIsps,
                    'total_customers' => $totalCustomers,
                    'total_revenue' => $totalRevenue,
                    'pending_approvals' => $pendingApprovals,
                    'active_users' => $activeUsers,
                    'monthly_revenue' => array_values($monthlyRevenue), // Returns array [jan_total, feb_total, ...]
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
                        'created_at' => $isp->created_at,
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

    public function search(Request $request)
    {
        $q = strtolower($request->query('q', ''));
        
        if (empty($q)) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // 1. Menu Search (Super Admin)
        $menus = [
            ['name' => 'ISP Management', 'description' => 'Kelola seluruh ISP terdaftar', 'to' => '/super-admin/isp-management', 'keywords' => ['isp', 'perusahaan', 'mitra']],
            ['name' => 'Subscription Packages', 'description' => 'Atur paket berlangganan ISP', 'to' => '/super-admin/subscription-packages', 'keywords' => ['paket', 'langganan', 'harga', 'package']],
            ['name' => 'ISP Order Management', 'description' => 'Kelola pesanan layanan dari ISP', 'to' => '/super-admin/isp-orders', 'keywords' => ['pesanan', 'order', 'masuk', 'pesanan masuk', 'trial']],
            ['name' => 'Withdrawal Requests', 'description' => 'Kelola permintaan penarikan saldo ISP', 'to' => '/super-admin/withdrawals', 'keywords' => ['penarikan', 'withdrawal', 'payout', 'bank']],
            ['name' => 'Server Manager', 'description' => 'Konfigurasi server Mikrotik/VPN', 'to' => '/super-admin/servers', 'keywords' => ['server', 'mikrotik', 'vpn', 'koneksi']],
            ['name' => 'Payment Gateways', 'description' => 'Atur metode pembayaran sistem', 'to' => '/super-admin/payment-gateways', 'keywords' => ['pembayaran', 'xendit', 'midtrans', 'bank']],
            ['name' => 'Landing Page Editor', 'description' => 'Edit tampilan utama website', 'to' => '/super-admin/landing-page-editor', 'keywords' => ['tampilan', 'landing', 'depan', 'desain']],
            ['name' => 'System Dashboard', 'description' => 'Ringkasan statistik sistem', 'to' => '/super-admin/dashboard', 'keywords' => ['statistik', 'grafik', 'dashboard', 'home']],
        ];

        foreach ($menus as $menu) {
            $match = str_contains(strtolower($menu['name']), $q) || 
                     str_contains(strtolower($menu['description']), $q) || 
                     collect($menu['keywords'])->contains(fn($k) => str_contains($k, $q));
            
            if ($match) {
                $results[] = [
                    'name' => $menu['name'],
                    'description' => 'Menu: ' . $menu['description'],
                    'icon' => 'bx-menu',
                    'to' => $menu['to']
                ];
            }
        }

        // 2. Data Search - ISPs
        $isps = ISP::where('company_name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->limit(3)
            ->get();

        foreach ($isps as $isp) {
            $results[] = [
                'name' => $isp->company_name,
                'description' => 'ISP: ' . $isp->email,
                'icon' => 'bx-building',
                'to' => '/super-admin/isp-management'
            ];
        }

        // 3. Data Search - ISPOrders (Pesanan)
        $orders = \App\Models\ISPOrder::where('reference', 'like', "%{$q}%")
            ->orWhere('service_name', 'like', "%{$q}%")
            ->orWhere('status', 'like', "%{$q}%")
            ->limit(3)
            ->get();

        foreach ($orders as $order) {
            $results[] = [
                'name' => "Pesanan #" . $order->reference,
                'description' => "Order: {$order->service_name} ({$order->status})",
                'icon' => 'bx-cart',
                'to' => '/super-admin/isp-orders'
            ];
        }

        // 4. Data Search - Withdrawals (Penarikan)
        $withdrawals = \App\Models\Withdrawal::where('bank_name', 'like', "%{$q}%")
            ->orWhere('account_name', 'like', "%{$q}%")
            ->limit(3)
            ->get();

        foreach ($withdrawals as $wd) {
            $results[] = [
                'name' => "Penarikan " . $wd->bank_name,
                'description' => "Transfer ke: {$wd->account_name} (Rp " . number_format($wd->amount, 0, ',', '.') . ")",
                'icon' => 'bx-wallet',
                'to' => '/super-admin/withdrawals'
            ];
        }

        return response()->json(['results' => $results]);
    }
}
