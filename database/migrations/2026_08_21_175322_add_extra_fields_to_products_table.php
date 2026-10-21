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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->string('item_location')->nullable();
            $table->decimal('minimum_stock_qty', 10, 2)->nullable()->default(0);
            $table->enum('discount_type', ['percentage', 'amount'])->nullable();
            $table->decimal('sale_discount', 10, 2)->nullable()->default(0);
            $table->boolean('inclusive_of_tax')->default(0);
            $table->unsignedBigInteger('secondary_unit_id')->nullable();
            $table->decimal('conversion_rate', 10, 4)->nullable();

            $table->foreign('secondary_unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['secondary_unit_id']);
            $table->dropColumn([
                'tax_rate',
                'item_location',
                'minimum_stock_qty',
                'discount_type',
                'sale_discount',
                'inclusive_of_tax',
                'secondary_unit_id',
                'conversion_rate'
            ]);
        });
    }
};
