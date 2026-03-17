<?php

namespace App\Http\Controllers\API\Shared;

use App\Http\Controllers\Controller;
use App\Models\ISP;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ISPOrder;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get a list of recent activities as notifications.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $notifications = [];

        if ($user->role === 'super_admin') {
            // Notifications for Super Admin: New ISP registrations, pending orders
            $recentIsps = ISP::orderBy('created_at', 'desc')->limit(5)->get();
            foreach ($recentIsps as $isp) {
                $notifications[] = [
                    'id' => 'isp_' . $isp->id,
                    'title' => 'Unit ISP Baru',
                    'message' => 'ISP ' . $isp->company_name . ' baru saja mendaftar.',
                    'time' => $isp->created_at->diffForHumans(),
                    'type' => 'info'
                ];
            }

            $pendingOrders = ISPOrder::where('status', 'pending')->orderBy('created_at', 'desc')->limit(5)->get();
            foreach ($pendingOrders as $order) {
                $notifications[] = [
                    'id' => 'order_' . $order->id,
                    'title' => 'Pesanan Baru',
                    'message' => 'Pesanan baru #' . $order->reference . ' menunggu konfirmasi.',
                    'time' => $order->created_at->diffForHumans(),
                    'type' => 'warning'
                ];
            }
        } elseif ($user->role === 'isp_admin') {
            // Notifications for ISP Admin: New customers, invoices
            $ispId = $user->isp_id;
            
            $recentCustomers = Customer::where('isp_id', $ispId)->orderBy('created_at', 'desc')->limit(5)->get();
            foreach ($recentCustomers as $customer) {
                $notifications[] = [
                    'id' => 'cust_' . $customer->id,
                    'title' => 'Pelanggan Baru',
                    'message' => 'Pelanggan ' . $customer->name . ' telah mendaftar.',
                    'time' => $customer->created_at->diffForHumans(),
                    'type' => 'info'
                ];
            }

            $unpaidInvoices = Invoice::where('isp_id', $ispId)->where('payment_status', 'unpaid')->orderBy('due_date', 'asc')->limit(5)->get();
            foreach ($unpaidInvoices as $invoice) {
                $notifications[] = [
                    'id' => 'inv_' . $invoice->id,
                    'title' => 'Tagihan Belum Dibayar',
                    'message' => 'Tagihan ' . $invoice->invoice_number . ' mendekati tanggal jatuh tempo.',
                    'time' => $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->diffForHumans() : 'N/A',
                    'type' => 'danger'
                ];
            }
        }

        // Sort by time (using unique IDs to keep stable sort)
        // Note: For simplicity, we just return the combined lists for now
        
        return response()->json([
            'notifications' => array_slice($notifications, 0, 10)
        ]);
    }
}
