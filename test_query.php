<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());
echo json_encode(\DB::select("SELECT id, name, barcode FROM products WHERE FIND_IN_SET('12323', REPLACE(barcode, ' ', '')) > 0 LIMIT 1"));
