<?php

namespace App\Console\Commands;

use App\Models\IspOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PackageInactive;

class CheckExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and mark expired orders that have passed payment deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired orders...');

        $expiredOrders = IspOrder::where('status', 'pending_payment')
            ->where('payment_expired_at', '<', now())
            ->whereNotNull('payment_expired_at')
            ->get();

        $count = 0;

        foreach ($expiredOrders as $order) {
            try {
                $order->markAsExpired();
                $count++;
                
                Log::info("Order {$order->reference} marked as expired", [
                    'order_id' => $order->id,
                    'isp_id' => $order->isp_id,
                    'payment_expired_at' => $order->payment_expired_at
                ]);

                $this->line("✓ Order {$order->reference} marked as expired");
                
                 // Send Email
                try {
                    if ($order->isp && $order->isp->email) {
                        Mail::to($order->isp->email)->send(new PackageInactive($order, 'cancelled'));
                    }
                } catch (\Exception $e) {
                     Log::error("Failed to send expiry email for order {$order->reference}: " . $e->getMessage());
                }
            } catch (\Exception $e) {
                Log::error("Failed to mark order {$order->reference} as expired: " . $e->getMessage());
                $this->error("✗ Failed to expire order {$order->reference}");
            }
        }

        $this->info("Processed {$count} expired orders.");

        return Command::SUCCESS;
    }
}
