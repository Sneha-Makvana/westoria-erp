<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Item Code from Excel goes here (separate from auto-generated SKU)
            $table->string('product_code', 100)->nullable()->after('SKU')->comment('Item Code from Excel import');

            // Additional Excel columns stored as nullable
            $table->string('supplier_code', 100)->nullable()->after('product_code')->comment('Supplier I/M Code');
            $table->string('international_code', 100)->nullable()->after('supplier_code')->comment('International I/M Code');
            $table->string('serial_no_status', 50)->nullable()->after('international_code')->comment('Serial No Status');
            $table->string('non_inventory_type', 100)->nullable()->after('serial_no_status')->comment('Non Inventory Type');
            $table->string('stock_validation_status', 50)->nullable()->after('non_inventory_type')->comment('Stock Validation Status');
            $table->string('item_type', 100)->nullable()->after('stock_validation_status')->comment('Item/Type from Excel');
            $table->string('discount_print_status', 50)->nullable()->after('item_type')->comment('Discount Print Status');
            $table->date('item_created_on')->nullable()->after('discount_print_status')->comment('Item Created On date from Excel');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_code',
                'supplier_code',
                'international_code',
                'serial_no_status',
                'non_inventory_type',
                'stock_validation_status',
                'item_type',
                'discount_print_status',
                'item_created_on',
            ]);
        });
    }
};
