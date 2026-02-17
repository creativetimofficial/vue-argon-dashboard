<?php

namespace App\Console\Commands;

use App\Models\IspOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOldExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cleanup-old-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Soft delete expired orders older than 90 days that were never paid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up old expired orders...');

        $cutoffDate = now()->subDays(90);

        $oldExpiredOrders = IspOrder::where('status', 'expired')
            ->where('payment_status', 'unpaid')
            ->where('updated_at', '<', $cutoffDate)
            ->get();

        $count = 0;

        foreach ($oldExpiredOrders as $order) {
            try {
                $order->delete(); // Soft delete
                $count++;
                
                Log::info("Old expired order {$order->reference} soft deleted", [
                    'order_id' => $order->id,
                    'isp_id' => $order->isp_id,
                    'expired_at' => $order->payment_expired_at,
                    'updated_at' => $order->updated_at
                ]);

                $this->line("✓ Order {$order->reference} soft deleted");
            } catch (\Exception $e) {
                Log::error("Failed to delete order {$order->reference}: " . $e->getMessage());
                $this->error("✗ Failed to delete order {$order->reference}");
            }
        }

        $this->info("Soft deleted {$count} old expired orders.");

        return Command::SUCCESS;
    }
}
