<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ISP;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingService
{
    /**
     * Generate recurring invoices for all eligible customers of an ISP.
     */
    public function generateRecurringInvoices(ISP $isp)
    {
        $customers = Customer::where('isp_id', $isp->id)
            ->where('status', 'active')
            ->whereNotNull('service_plan_id')
            ->where(function ($query) {
                $query->whereNull('next_billing_date')
                      ->orWhere('next_billing_date', '<=', now()->toDateString());
            })
            ->get();

        $generatedCount = 0;
        foreach ($customers as $customer) {
            try {
                $this->createInvoiceForCustomer($customer);
                $generatedCount++;
            } catch (\Exception $e) {
                \Log::error("Failed to generate invoice for customer {$customer->id}: " . $e->getMessage());
            }
        }

        return $generatedCount;
    }

    /**
     * Create a single invoice for a customer based on their service plan.
     */
    public function createInvoiceForCustomer(Customer $customer)
    {
        return DB::transaction(function () use ($customer) {
            $service = $customer->servicePlan; // Using the relationship from migration: service_plan_id -> isp_services

            if (!$service) {
                throw new \Exception("Customer has no assigned service plan.");
            }

            // Calculate Period
            $startDate = $customer->next_billing_date ? Carbon::parse($customer->next_billing_date) : now();
            $endDate = $this->calculateNextBillingDate($startDate, $service->billing_cycle);

            // Calculate PPN (Assuming 11% standard, or make it dynamic per ISP later)
            $subtotal = $service->price;
            $taxRate = 0.11; // 11% PPN
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            $invoice = Invoice::create([
                'isp_id' => $customer->isp_id,
                'invoice_number' => $this->generateInvoiceNumber($customer->isp),
                'billable_type' => Customer::class,
                'billable_id' => $customer->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_status' => 'unpaid',
                'status' => 'published',
                'issue_date' => now(),
                'due_date' => now()->addDays(7), // Default 7 days grace period
                'period_start' => $startDate,
                'period_end' => $endDate->subDay(),
                'notes' => "Tagihan layanan internet periode {$startDate->format('d/m/Y')} - {$endDate->subDay()->format('d/m/Y')}",
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => "Paket Internet: " . $service->name,
                'quantity' => 1,
                'unit_price' => $subtotal,
                'total' => $subtotal,
            ]);

            // Update Customer next billing date
            $customer->update([
                'next_billing_date' => $endDate->toDateString()
            ]);

            return $invoice;
        });
    }

    /**
     * Calculate next billing date based on cycle.
     */
    private function calculateNextBillingDate(Carbon $currentDate, $cycle)
    {
        $date = $currentDate->copy();
        switch ($cycle) {
            case 'daily': return $date->addDay();
            case 'weekly': return $date->addWeek();
            case 'monthly': return $date->addMonth();
            case 'quarterly': return $date->addMonths(3);
            case 'semi_annual': return $date->addMonths(6);
            case 'annual': return $date->addYear();
            default: return $date->addMonth();
        }
    }

    /**
     * Generate a unique invoice number.
     */
    private function generateInvoiceNumber(ISP $isp)
    {
        $prefix = strtoupper(substr($isp->company_name, 0, 3));
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(4));
        return "INV-{$prefix}-{$date}-{$random}";
    }
}
