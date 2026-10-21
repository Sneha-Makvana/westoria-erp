<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $model = new \App\Models\LeaveModel();
    print_r($model->getEmployeeReport());
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
