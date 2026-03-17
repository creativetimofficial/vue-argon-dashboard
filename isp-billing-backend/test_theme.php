<?php
// Test script to check getPublicTheme response

use App\Models\ISP;
use App\Models\IspTheme;
use Illuminate\Http\Request;

// Simulate a request for irvan1.localhost
$_SERVER['HTTP_HOST'] = 'irvan1.localhost';

// Use artisan tinker logic or a simple script
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$host = 'irvan1.localhost';
$parts = explode('.', $host);
$subdomain = $parts[0];

echo "Testing Host: $host\n";
echo "Subdomain: $subdomain\n";

$isp = ISP::where('subdomain', $subdomain)->first();
if ($isp) {
    echo "Found ISP: " . $isp->company_name . " (ID: " . $isp->id . ")\n";
    $theme = IspTheme::where('isp_id', $isp->id)->first();
    if ($theme) {
        echo "Found ISP Theme: " . ($theme->dark_mode_default ? 'Dark' : 'Light') . "\n";
    } else {
        echo "No ISP Theme found.\n";
    }
} else {
    echo "ISP not found.\n";
}

// Test the actual logic from controller
$isp_bound = ISP::where('subdomain', $subdomain)->first();
if ($isp_bound) {
    app()->instance('current_isp', $isp_bound);
}

$isp = app()->bound('current_isp') ? app('current_isp') : null;
if ($isp) {
    $theme = IspTheme::where('isp_id', $isp->id)->where('is_active', true)->first();
    echo "Final Theme Result: " . ($theme ? ($theme->dark_mode_default ? 'Dark' : 'Light') : 'NULL') . "\n";
}
