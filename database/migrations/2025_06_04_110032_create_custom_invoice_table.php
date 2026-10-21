<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_invoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->json('products')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid', 10, 2)->nullable()->default(0);
            $table->decimal('discount', 10, 2)->nullable()->default(0);
            $table->decimal('shipping', 10, 2)->nullable()->default(0);
            $table->text('taxes')->nullable();
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->nullable()->default(0);
            $table->string('gst_option')->nullable()->default('without_gst');
            $table->string('status')->default('pending');
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_invoice');
    }
};
