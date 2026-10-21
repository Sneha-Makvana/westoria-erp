<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('row_material_purchase_invoice', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('branch_id')->nullable();

            $table->string('invoice_number')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('bill_no')->nullable();

            $table->json('materials')->nullable();

            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('shipping', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);

            $table->string('gst_option')->nullable();
            $table->string('status')->nullable();

            $table->json('taxes')->nullable();

            $table->boolean('isDeleted')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index('branch_id');
            $table->index('vendor_id');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('row_material_purchase_invoice');
    }
};