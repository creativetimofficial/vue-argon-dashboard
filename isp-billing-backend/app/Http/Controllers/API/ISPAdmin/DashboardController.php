<?php

namespace App\Http\Controllers\API\ISPAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ISP;
use App\Models\ISPOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get real-time statistics for the ISP Admin dashboard.
     */
    public function stats()
    {
        $ispId = auth()->user()->isp_id;
        $isp = auth()->user()->isp()->with('subscriptionPackage')->first();

        // 1. Customer Stats
        $totalCustomers = Customer::where('isp_id', $ispId)->count();
        $activeCustomers = Customer::where('isp_id', $ispId)->where('status', 'active')->count();
        $suspendedCustomers = Customer::where('isp_id', $ispId)->where('status', 'suspended')->count();

        // 2. Financial Stats (This Month)
        $thisMonthStart = now()->startOfMonth();
        $revenueThisMonth = Invoice::where('isp_id', $ispId)
            ->where('payment_status', 'paid')
            ->where('paid_at', '>=', $thisMonthStart)
            ->sum('total');

        $pendingAmount = Invoice::where('isp_id', $ispId)
            ->where('payment_status', 'unpaid')
            ->sum('total');

        $overdueCount = Invoice::where('isp_id', $ispId)
            ->where('payment_status', 'unpaid')
            ->where('due_date', '<', now())
            ->count();

        // 3. Subscription Info
        $subscription = $isp ? $isp->subscriptionPackage : null;

        return response()->json([
            'stats' => [
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'suspended_customers' => $suspendedCustomers,
                'revenue_this_month' => (float)$revenueThisMonth,
                'pending_amount' => (float)$pendingAmount,
                'overdue_invoices' => $overdueCount,
            ],
            'isp' => $isp,
            'subscription' => $subscription,
            'recent_customers' => Customer::where('isp_id', $ispId)->orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_invoices' => Invoice::where('isp_id', $ispId)->with('billable')->orderBy('created_at', 'desc')->limit(5)->get(),
        ]);
    }

    /**
     * Global search through ISP data handling user input from Navbar.
     */
    public function search(Request $request)
    {
        $ispId = auth()->user()->isp_id;
        $q = strtolower($request->query('q', ''));
        
        if (empty($q)) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // 1. Menu Search (ISP/Client Area)
        $menus = [
            ['name' => 'Dashboard', 'description' => 'Ringkasan statistik akun', 'to' => '/client-area/dashboard', 'keywords' => ['statistik', 'home', 'beranda', 'utama']],
            ['name' => 'My Services', 'description' => 'Layanan VPN/Internet aktif Anda', 'to' => '/client-area/my-services', 'keywords' => ['layanan', 'vpn', 'internet', 'aktif']],
            ['name' => 'Billing & Invoices', 'description' => 'Tagihan dan riwayat pembayaran', 'to' => '/client-area/invoices', 'keywords' => ['tagihan', 'pembayaran', 'invoice', 'bayar']],
            ['name' => 'Order New Service', 'description' => 'Langganan layanan baru', 'to' => '/client-area/orders', 'keywords' => ['beli', 'order', 'pesan', 'tambah']],
            ['name' => 'Topup Balance', 'description' => 'Isi ulang saldo akun', 'to' => '/client-area/topup', 'keywords' => ['isi', 'saldo', 'topup', 'tambah', 'duit']],
            ['name' => 'Withdrawal Balance', 'description' => 'Tarik saldo referral/komisi', 'to' => '/client-area/withdraw', 'keywords' => ['tarik', 'komisi', 'saldo', 'payout']],
            ['name' => 'My Profile', 'description' => 'Pengaturan akun dan profil', 'to' => '/client-area/profile', 'keywords' => ['profil', 'akun', 'password', 'setting']],
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

        // 2. Data Search - Customers (for ISP Admin)
        $customers = Customer::where('isp_id', $ispId)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('mikrotik_username', 'like', "%{$q}%");
            })
            ->limit(3)
            ->get();

        foreach ($customers as $c) {
            $results[] = [
                'name' => $c->name,
                'description' => 'Pelanggan: ' . ($c->pppoe_username ?? $c->email),
                'icon' => 'bx-user',
                'to' => '/isp-admin/customers'
            ];
        }

        // 3. Data Search - Invoices
        $invoices = Invoice::where('isp_id', $ispId)
            ->where('invoice_number', 'like', "%{$q}%")
            ->limit(3)
            ->get();

        foreach ($invoices as $inv) {
            $results[] = [
                'name' => $inv->invoice_number,
                'description' => 'Nominal: Rp ' . number_format($inv->total, 0, ',', '.'),
                'icon' => 'bx-receipt',
                'to' => '/client-area/invoices'
            ];
        }

        // 4. Data Search - Owned ISPs (For Client Area owners)
        $ownedIsps = ISP::where('owner_id', auth()->id())
            ->where(function ($query) use ($q) {
                $query->where('company_name', 'like', "%{$q}%")
                      ->orWhere('subdomain', 'like', "%{$q}%");
            })
            ->limit(3)
            ->get();

        foreach ($ownedIsps as $isp) {
            $results[] = [
                'name' => $isp->company_name,
                'description' => 'Unit ISP: ' . ($isp->subdomain ? $isp->subdomain . '.domain' : 'Baru'),
                'icon' => 'bx-building',
                'to' => '/client-area/services'
            ];
        }

        // 5. Data Search - ISP Orders
        $orders = \App\Models\ISPOrder::whereIn('isp_id', ISP::where('owner_id', auth()->id())->pluck('id'))
            ->where(function ($query) use ($q) {
                $query->where('reference', 'like', "%{$q}%")
                      ->orWhere('service_name', 'like', "%{$q}%");
            })
            ->limit(3)
            ->get();

        foreach ($orders as $order) {
            $results[] = [
                'name' => 'Pesanan #' . $order->reference,
                'description' => 'Layanan: ' . $order->service_name . ' (' . $order->status . ')',
                'icon' => 'bx-cart',
                'to' => '/client-area/orders'
            ];
        }

        return response()->json(['results' => $results]);
    }
}
