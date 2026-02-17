<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ISPOrder;
use App\Models\Server;
use App\Services\MikrotikService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PackageInactive;
use App\Mail\PackageExpiringSoon;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'isp:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and remove VPN accounts from Mikrotik';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions and sending reminders...');

        // 1. Send Reminder emails (3 days before expiry)
        // Find active orders where expired_date is "today + 3 days"
        // We use startOfDay to match dates regardless of time, or between range
        $targetDate = Carbon::now()->addDays(3)->format('Y-m-d');
        
        $expiringOrders = ISPOrder::where('status', 'active')
            ->whereDate('expired_date', $targetDate)
            ->get();

        if ($expiringOrders->count() > 0) {
            $this->info("Found {$expiringOrders->count()} orders expiring in 3 days. Sending reminders...");
            foreach ($expiringOrders as $order) {
                 try {
                    if ($order->isp && $order->isp->email) {
                        Mail::to($order->isp->email)->send(new PackageExpiringSoon($order));
                        $this->info("  - Reminder sent to {$order->isp->email} (Order #{$order->reference})");
                    }
                } catch (\Exception $e) {
                     $this->error("  - Failed to send reminder: " . $e->getMessage());
                }
            }
        }

        // 2. Process Expired Orders (Grace Period Check)
        // Apply 3-Day Grace Period: Only expire if expired_date < now() - 3 days
        $gracePeriodDate = Carbon::now()->subDays(3);
        
        $expiredOrders = ISPOrder::where('status', 'active')
            ->where('expired_date', '<', $gracePeriodDate)
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No subscriptions to expire (Grace period active).');
        } else {
            $this->info("Found {$expiredOrders->count()} subscriptions past grace period. Processing expiry...");
            
            $mikrotik = new MikrotikService();

            foreach ($expiredOrders as $order) {
                $this->processExpiredOrder($order, $mikrotik);
            }
        }

        $this->info('Subscription check completed.');
    }

    private function processExpiredOrder($order, $mikrotik)
    {
        $this->info("Processing Order #{$order->reference} (User: {$order->username})...");

        // 1. Remove from Mikrotik if Server exists
        if ($order->server_id && $order->username) {
            $server = Server::find($order->server_id);
            
            if ($server && $server->ip_address) {
                // Connect
                $apiUser = $server->username ?? env('MIKROTIK_USER', 'admin');
                $apiPass = $server->password ?? env('MIKROTIK_PASS', '');
                
                try {
                    if ($mikrotik->connect($server->ip_address, $apiUser, $apiPass, $server->api_port ?? 8728)) {
                        // Remove Secret
                        if ($mikrotik->removePppSecret($order->username)) {
                            $this->info("  - Removed PPP Secret from Mikrotik.");
                            
                            // Decrease current users count (optional, but good for accuracy)
                            if ($server->current_users > 0) {
                                $server->decrement('current_users');
                            }
                        } else {
                            $this->warn("  - Failed to remove PPP Secret (User might not exist).");
                        }
                        $mikrotik->disconnect();
                    } else {
                        $this->error("  - Could not connect to Mikrotik Server.");
                    }
                } catch (\Exception $e) {
                    $this->error("  - Exception: " . $e->getMessage());
                    Log::error("Expiry Check Error: " . $e->getMessage());
                }
            }
        }

        // 2. Update Order Status
        $order->update([
            'status' => 'expired',
            'is_active' => false, // If there's such a flag
        ]);
        
        $this->info("  - Order status updated to expired.");
        
        // Send Email
        try {
            if ($order->isp && $order->isp->email) {
                Mail::to($order->isp->email)->send(new PackageInactive($order, 'expired'));
                $this->info("  - Expiry email sent.");
            }
        } catch (\Exception $e) {
             $this->error("  - Failed to send expiry email: " . $e->getMessage());
        }
    }
}
