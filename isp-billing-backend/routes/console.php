<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Check for expired orders every 5 minutes
Schedule::command('orders:check-expired')->everyFiveMinutes();

// Cleanup old expired orders monthly
Schedule::command('orders:cleanup-old-expired')->monthly();

// Check for expired subscriptions and send alerts (Hourly for robustness)
Schedule::command('isp:check-expiry')->hourly();
