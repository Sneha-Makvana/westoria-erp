<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\Schema::table('product_price_updates', function (\Illuminate\Database\Schema\Blueprint $table) {
    if (!\Illuminate\Support\Facades\Schema::hasColumn('product_price_updates', 'old_mrp')) {
        $table->decimal('old_mrp', 15, 2)->nullable()->after('old_price');
        $table->decimal('new_mrp', 15, 2)->nullable()->after('new_price');
        $table->decimal('old_purchase_price', 15, 2)->nullable()->after('old_mrp');
        $table->decimal('new_purchase_price', 15, 2)->nullable()->after('new_mrp');
    }
});

echo "Success";
