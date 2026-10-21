<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
$order = Order::where('order_number', 'SI/HO/7')->first();
if ($order) {
    echo "Found order: ID = " . $order->id . ", User ID = " . $order->user_id . "\n";
} else {
    echo "Order SI/HO/7 not found in database!\n";
}
