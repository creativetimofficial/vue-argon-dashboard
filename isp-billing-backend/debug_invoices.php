<?php

use App\Models\Invoice;
use App\Models\ISP;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoices = Invoice::all();

echo "Total Invoices: " . $invoices->count() . "\n";
foreach ($invoices as $invoice) {
    echo "ID: {$invoice->id} | Status: {$invoice->status} | Payment Status: {$invoice->payment_status} | Total: {$invoice->total}\n";
}
