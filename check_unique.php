<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$indices = DB::select("SHOW INDEX FROM orders WHERE Column_name='order_number' AND Non_unique=0");
echo count($indices) > 0 ? "Unique constraint exists" : "No unique constraint on order_number";
