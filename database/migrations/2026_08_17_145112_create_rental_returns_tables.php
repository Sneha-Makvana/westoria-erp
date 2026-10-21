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
        if (!Schema::hasTable('rental_returns')) {
            Schema::create('rental_returns', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('returned_by')->nullable();
                $table->date('return_date')->nullable();
                $table->decimal('total_late_fee', 10, 2)->default(0);
                $table->decimal('total_damage_charge', 10, 2)->default(0);
                $table->decimal('final_settlement_amount', 10, 2)->default(0);
                $table->string('payment_method')->nullable();
                $table->decimal('refund_amount', 10, 2)->default(0);
                $table->text('remarks')->nullable();
                $table->boolean('is_full_return')->default(0);
                $table->boolean('deposit_settled')->default(0);
                $table->decimal('deposit_amount_applied', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rental_return_items')) {
            Schema::create('rental_return_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('rental_return_id')->nullable();
                $table->unsignedBigInteger('order_item_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('variant_id')->nullable();
                $table->integer('qty_returned')->default(0);
                $table->string('condition')->nullable();
                $table->decimal('damage_charge', 10, 2)->default(0);
                $table->decimal('late_fee', 10, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_return_items');
        Schema::dropIfExists('rental_returns');
    }
};
