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
        // 1. Add fields to products table
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'is_rent_available')) {
                    $table->tinyInteger('is_rent_available')->default(0)->after('price');
                }
                if (!Schema::hasColumn('products', 'rent_period')) {
                    $table->string('rent_period')->nullable()->after('is_rent_available');
                }
                if (!Schema::hasColumn('products', 'rent_price')) {
                    $table->decimal('rent_price', 15, 6)->nullable()->after('rent_period');
                }
            });
        }

        // 2. Add fields to orders table
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'rental_status')) {
                    $table->string('rental_status')->default('sales')->after('payment_status');
                }
                if (!Schema::hasColumn('orders', 'return_status')) {
                    $table->string('return_status')->nullable()->after('rental_status');
                }
            });
        }

        // 3. Add fields to order_items table
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'is_rental')) {
                    $table->tinyInteger('is_rental')->default(0)->after('price');
                }
                if (!Schema::hasColumn('order_items', 'rent_value')) {
                    $table->integer('rent_value')->nullable()->after('is_rental');
                }
                if (!Schema::hasColumn('order_items', 'rent_period')) {
                    $table->string('rent_period')->nullable()->after('rent_value');
                }
                if (!Schema::hasColumn('order_items', 'rent_price')) {
                    $table->decimal('rent_price', 15, 6)->nullable()->after('rent_period');
                }
                if (!Schema::hasColumn('order_items', 'rental_quantity')) {
                    $table->integer('rental_quantity')->nullable()->after('quantity');
                }
                if (!Schema::hasColumn('order_items', 'rent_start_at')) {
                    $table->dateTime('rent_start_at')->nullable()->after('rent_price');
                }
                if (!Schema::hasColumn('order_items', 'rent_end_at')) {
                    $table->dateTime('rent_end_at')->nullable()->after('rent_start_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['is_rent_available', 'rent_period', 'rent_price']);
            });
        }
        
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['rental_status', 'return_status']);
            });
        }
        
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn(['is_rental', 'rent_value', 'rent_period', 'rent_price', 'rental_quantity', 'rent_start_at', 'rent_end_at']);
            });
        }
    }
};
