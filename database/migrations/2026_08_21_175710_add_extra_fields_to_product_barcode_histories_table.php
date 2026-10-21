<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_barcode_histories', function (Blueprint $table) {
            $table->decimal('old_tax_rate', 5, 2)->nullable();
            $table->decimal('new_tax_rate', 5, 2)->nullable();
            $table->decimal('old_sale_discount', 10, 2)->nullable();
            $table->decimal('new_sale_discount', 10, 2)->nullable();
            $table->enum('old_discount_type', ['percentage', 'amount'])->nullable();
            $table->enum('new_discount_type', ['percentage', 'amount'])->nullable();
            $table->decimal('old_minimum_stock_qty', 10, 2)->nullable();
            $table->decimal('new_minimum_stock_qty', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_barcode_histories', function (Blueprint $table) {
            $table->dropColumn([
                'old_tax_rate',
                'new_tax_rate',
                'old_sale_discount',
                'new_sale_discount',
                'old_discount_type',
                'new_discount_type',
                'old_minimum_stock_qty',
                'new_minimum_stock_qty',
            ]);
        });
    }
};
