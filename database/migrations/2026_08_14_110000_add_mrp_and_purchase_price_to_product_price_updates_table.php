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
        Schema::table('product_price_updates', function (Blueprint $table) {
            $table->decimal('old_mrp', 15, 2)->nullable()->after('old_price');
            $table->decimal('new_mrp', 15, 2)->nullable()->after('new_price');
            $table->decimal('old_purchase_price', 15, 2)->nullable()->after('old_mrp');
            $table->decimal('new_purchase_price', 15, 2)->nullable()->after('new_mrp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_price_updates', function (Blueprint $table) {
            $table->dropColumn([
                'old_mrp',
                'new_mrp',
                'old_purchase_price',
                'new_purchase_price'
            ]);
        });
    }
};
