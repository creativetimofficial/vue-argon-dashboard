<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$gateway = \App\Models\PaymentGateway::where('gateway_name', 'Midtrans')->first();
if ($gateway) {
    echo "Gateway: " . $gateway->gateway_name . "\n";
    echo "Is Active: " . ($gateway->is_active ? 'Yes' : 'No') . "\n";
    echo "Sandbox Mode: " . ($gateway->sandbox_mode ? 'Yes (1)' : 'No (0)') . "\n";
    // Masked keys
    $serverKey = $gateway->secret_key ?? ($gateway->settings['server_key'] ?? '');
    echo "Server Key (Masked): " . substr($serverKey, 0, 5) . "..." . substr($serverKey, -5) . "\n";
} else {
    echo "Midtrans Gateway not found in DB.\n";
}
