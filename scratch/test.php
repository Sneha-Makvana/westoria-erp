<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all(['id', 'name', 'role', 'branch_id'])->toArray();
$updates = \App\Models\ProductPriceUpdate::all()->toArray();
file_put_contents(__DIR__.'/scratch/db_dump.json', json_encode(['users' => $users, 'updates' => $updates], JSON_PRETTY_PRINT));
echo "Dumped";
