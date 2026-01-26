<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $migrations = Illuminate\Support\Facades\DB::table('migrations')->get();
    $output = "Migrations in DB:\n";
    foreach($migrations as $m) {
        $output .= $m->migration . " | Batch: " . $m->batch . "\n";
    }
    file_put_contents('migrations_debug.txt', $output);
} catch (Exception $e) {
    file_put_contents('migrations_debug.txt', "Error: " . $e->getMessage());
}
