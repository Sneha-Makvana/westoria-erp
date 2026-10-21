<?php
$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';
$app = require_once $projectRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\admin\SalesController;

// login as user id 1
Auth::loginUsingId(1);

// build request payload similar to web form
$params = [
    'order_id' => 2,
    'items' => [
        ['order_item_id' => 4, 'delivered_quantity' => 1],
        ['order_item_id' => 5, 'delivered_quantity' => 1],
    ],
];

$request = Request::create('/sales/delivery/store', 'POST', $params);
// call controller
$ctrl = new SalesController();
$response = $ctrl->storeDelivery($request);

echo "Controller response type: " . (is_string($response) ? 'string' : get_class($response)) . PHP_EOL;
if (is_object($response) && method_exists($response, 'getStatusCode')) {
    echo "Status: " . $response->getStatusCode() . PHP_EOL;
}
echo "Done\n";
