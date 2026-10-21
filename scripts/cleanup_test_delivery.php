<?php
$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';
$app = require_once $projectRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$deleted = DB::table('deliveries')->where('order_id', 2)->where('order_item_id', 4)->delete();
echo "Deleted rows: $deleted\n";
