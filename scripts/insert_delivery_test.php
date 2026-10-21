<?php
$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';
$app = require_once $projectRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use App\Models\Delivery;
use App\Models\OrderItem;

$orderItem = OrderItem::find(4);
if (! $orderItem) {
    echo "OrderItem 4 not found\n";
    exit(1);
}
try {
    $del = Delivery::create([
        'order_id' => $orderItem->order_id,
        'order_item_id' => $orderItem->id,
        'product_id' => $orderItem->product_id,
        'delivered_quantity' => 1,
        'ordered_quantity' => $orderItem->quantity,
        'status' => 'partially_delivered',
        'delivered_by' => 1,
        'delivered_at' => date('Y-m-d H:i:s'),
    ]);
    echo "Inserted delivery id=" . ($del->id ?? 'null') . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
