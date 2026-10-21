<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('row_material', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            $table->string('row_materialname');
            $table->string('SKU')->nullable();
            $table->string('hsn_code')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->text('description')->nullable();

            $table->decimal('price', 15, 2)->default(0);
            $table->text('images')->nullable();
            $table->decimal('quantity', 15, 3)->default(0);

            $table->unsignedBigInteger('unit_id')->nullable();

            $table->boolean('isDeleted')->default(0);
            $table->boolean('availablility')->default(1);
            $table->boolean('status')->default(1);

            $table->string('gst_option')->nullable();
            $table->text('product_gst')->nullable();

            $table->unsignedBigInteger('create_by')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('branch_id');
            $table->index('unit_id');
            $table->index('create_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('row_material');
    }
};