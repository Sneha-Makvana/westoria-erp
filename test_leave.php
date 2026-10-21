<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = \App\Models\User::first();
    auth('web')->login($user);

    $request = \Illuminate\Http\Request::create('/api/leave', 'POST', [
        'user_id' => $user->id,
        'leave_id' => 7,
        'reason' => 'test',
        'start_date' => '2026-05-28',
        'end_date' => '2026-05-30',
        'status' => 'Pending'
    ]);
    $request->headers->set('Accept', 'application/json');
    $controller = app(\App\Http\Controllers\admin\LeaveController::class);
    $res = $controller->store($request);
    echo $res->getStatusCode();
    echo "\n";
    echo $res->getContent();
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
