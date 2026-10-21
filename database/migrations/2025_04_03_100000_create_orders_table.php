<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->decimal('discount', 10, 2)->nullable()->default(0);
            $table->decimal('shipping', 10, 2)->nullable()->default(0);
            $table->text('tax_id')->nullable();
            $table->string('gst_option')->default('without_gst');
            $table->decimal('tds_percentage', 5, 2)->default(0);
            $table->decimal('tds_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('remaining_amount', 10, 2)->nullable()->default(0);
            $table->string('payment_status')->default('pending');
            $table->string('delivery_status')->nullable()->default('pending');
            $table->string('payment_method')->default('cash');
            $table->string('order_invoice')->nullable();
            $table->boolean('quotation_status')->default(0);
            $table->string('approved_status')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
