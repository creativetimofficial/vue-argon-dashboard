<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- DIAGNOSTIC START ---\n";

// 1. Test Logging
Log::info("DIAGNOSTIC TEST LOG ENTRY: " . now());
echo "Log entry written. Check laravel.log for 'DIAGNOSTIC TEST LOG ENTRY'.\n";

// 2. Check Withdrawals Table
if (Schema::hasTable('withdrawals')) {
    echo "Table 'withdrawals' exists.\n";
} else {
    echo "ERROR: Table 'withdrawals' does NOT exist.\n";
}

// 3. Check Pending Topups
$pending = \App\Models\Payment::where('status', 'pending')
    ->where('transaction_id', 'like', 'TOP-%')
    ->latest()
    ->first();

if ($pending) {
    echo "Latest Pending Topup: " . $pending->transaction_id . " Amount: " . $pending->amount . "\n";
} else {
    echo "No pending topups found.\n";
}

echo "--- DIAGNOSTIC END ---\n";
