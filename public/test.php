<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = \App\Models\Product::where('SKU', '123')->first();
echo "Product ID: {$product->id}\n";
echo "MRP: {$product->mrp}\n";
echo "Purchase Price: {$product->purchase_price}\n";
