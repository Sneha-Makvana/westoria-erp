<?php
$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';
$app = require_once $projectRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$orderId = $argv[1] ?? 3;
$rows = DB::table('deliveries')->where('order_id', $orderId)->orderByDesc('id')->limit(50)->get();
echo "Found " . count($rows) . " rows for order_id={$orderId}\n";
foreach ($rows as $r) {
    echo "id={$r->id} order_item_id={$r->order_item_id} product_id={$r->product_id} qty={$r->delivered_quantity} at={$r->delivered_at}\n";
}
