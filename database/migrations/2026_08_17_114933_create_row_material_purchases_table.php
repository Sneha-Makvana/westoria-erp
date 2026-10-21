<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('row_material_purchases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('item')->nullable();

            $table->decimal('quantity', 15, 3)->default(0);

            $table->json('product_gst_details')->nullable();
            $table->decimal('product_gst_total', 15, 2)->default(0);

            $table->decimal('price', 15, 2)->default(0);

            $table->string('purchase_status')->nullable();
            $table->string('payment_status')->nullable();

            $table->decimal('amount_total', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('discount_percent', 8, 2)->default(0);

            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();

            $table->boolean('isDeleted')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->index('branch_id');
            $table->index('item');
            $table->index('vendor_id');
            $table->index('invoice_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('row_material_purchases');
    }
};