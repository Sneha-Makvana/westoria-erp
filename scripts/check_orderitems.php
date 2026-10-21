<?php
$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';
$app = require_once $projectRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$ids = $argv;
array_shift($ids);
if (empty($ids)) {
    echo "Usage: php scripts/check_orderitems.php <id1> <id2> ...\n";
    exit(1);
}
$rows = DB::table('order_items')->whereIn('id', $ids)->get();
echo "Found " . count($rows) . " order_items\n";
foreach ($rows as $r) {
    echo "id={$r->id} order_id={$r->order_id} product_id={$r->product_id} qty={$r->quantity} price={$r->price}\n";
}
