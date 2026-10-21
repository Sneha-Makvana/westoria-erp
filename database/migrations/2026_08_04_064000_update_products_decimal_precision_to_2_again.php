<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->nullable()->change();
            if (Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->nullable()->change();
            }
            if (Schema::hasColumn('products', 'landing_cost')) {
                $table->decimal('landing_cost', 15, 2)->nullable()->change();
            }
            if (Schema::hasColumn('products', 'rent_price')) {
                $table->decimal('rent_price', 15, 2)->nullable()->change();
            }
        });
        
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'price')) {
                $table->decimal('price', 15, 2)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 20, 6)->nullable()->change();
            if (Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 20, 6)->nullable()->change();
            }
            if (Schema::hasColumn('products', 'landing_cost')) {
                $table->decimal('landing_cost', 20, 6)->nullable()->change();
            }
        });
        
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'price')) {
                $table->decimal('price', 20, 6)->nullable()->change();
            }
        });
    }
};
