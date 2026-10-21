<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_invoice_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('item')->nullable();           // product_id
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->json('product_gst_details')->nullable();
            $table->decimal('product_gst_total', 10, 2)->nullable()->default(0);
            $table->string('invoice_status')->nullable();
            $table->enum('purchase_status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->decimal('amount_total', 10, 2)->default(0);
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            // Payment breakdown
            $table->string('payment_mode')->nullable();
            $table->string('paid_type')->nullable();
            $table->decimal('cash_amount', 10, 2)->default(0);
            $table->decimal('upi_amount', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->decimal('pending_amount', 10, 2)->default(0);
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('custom_invoice')->onDelete('set null');
            $table->foreign('item')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_invoice_item');
    }
};
