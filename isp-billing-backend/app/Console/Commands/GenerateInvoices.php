<?php

namespace App\Console\Commands;

use App\Models\ISP;
use App\Services\BillingService;
use Illuminate\Console\Command;

class GenerateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate {isp_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate recurring invoices for ISP customers';

    /**
     * Execute the console command.
     */
    public function handle(BillingService $billingService)
    {
        $ispId = $this->argument('isp_id');

        if ($ispId) {
            $isp = ISP::find($ispId);
            if (!$isp) {
                $this->error("ISP with ID {$ispId} not found.");
                return 1;
            }
            $isps = collect([$isp]);
        } else {
            $isps = ISP::where('is_active', true)->get();
        }

        $this->info("Starting invoice generation for " . $isps->count() . " ISP(s)...");

        foreach ($isps as $isp) {
            $this->info("Processing ISP: {$isp->company_name}");
            $count = $billingService->generateRecurringInvoices($isp);
            $this->comment("Generated {$count} invoices for {$isp->company_name}");
        }

        $this->info("Invoice generation completed.");
        return 0;
    }
}
