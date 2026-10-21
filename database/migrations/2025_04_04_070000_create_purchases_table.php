<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('item')->nullable();          // product_id
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->json('product_gst_details')->nullable();
            $table->decimal('product_gst_total', 10, 2)->nullable()->default(0);
            $table->enum('purchase_status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->decimal('amount_total', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->nullable()->default(0);
            $table->decimal('discount_percent', 5, 2)->nullable()->default(0);
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('invoice_id')->references('id')->on('purchase_invoice')->onDelete('set null');
            $table->foreign('item')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
