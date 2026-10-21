<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Update the products table decimal columns to support 6 decimal places.
 *
 * Columns changed:
 *   price         decimal(10,2)  → decimal(20,6)
 *   cost_price    decimal(12,2)  → decimal(20,6)
 *   landing_cost  decimal(12,2)  → decimal(20,6)
 *   quantity      decimal(15,3)  → decimal(20,6)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 20, 6)->nullable()->change();
            $table->decimal('quantity', 20, 6)->nullable()->default(0)->change();

            if (Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 20, 6)->nullable()->change();
            }

            if (Schema::hasColumn('products', 'landing_cost')) {
                $table->decimal('landing_cost', 20, 6)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->decimal('quantity', 15, 3)->nullable()->default(0)->change();

            if (Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->nullable()->change();
            }

            if (Schema::hasColumn('products', 'landing_cost')) {
                $table->decimal('landing_cost', 12, 2)->nullable()->change();
            }
        });
    }
};
