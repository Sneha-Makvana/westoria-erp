<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$columns = DB::select("DESCRIBE orders");
foreach($columns as $col) {
    if ($col->Field === 'order_number') {
        echo json_encode($col);
    }
}
