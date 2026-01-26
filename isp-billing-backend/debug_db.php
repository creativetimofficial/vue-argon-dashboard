<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $columns = Illuminate\Support\Facades\DB::select('DESCRIBE isp_orders');
    $output = "";
    foreach($columns as $column) {
        $output .= "Field: " . $column->Field . " | Type: " . $column->Type . " | Null: " . $column->Null . "\n";
    }
    file_put_contents('db_debug.txt', $output);
    echo "Done";
} catch (Exception $e) {
    file_put_contents('db_debug.txt', "Error: " . $e->getMessage());
    echo "Error";
}
