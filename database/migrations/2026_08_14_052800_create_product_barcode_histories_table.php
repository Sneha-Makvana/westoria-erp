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
        Schema::create('product_barcode_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('old_barcode')->nullable();
            $table->string('new_barcode')->nullable();
            $table->decimal('old_price', 15, 2)->nullable();
            $table->decimal('new_price', 15, 2)->nullable();
            $table->decimal('old_mrp', 15, 2)->nullable();
            $table->decimal('new_mrp', 15, 2)->nullable();
            $table->string('old_batch_no')->nullable();
            $table->string('new_batch_no')->nullable();
            $table->string('old_label_code')->nullable();
            $table->string('new_label_code')->nullable();
            $table->string('old_label_reference')->nullable();
            $table->string('new_label_reference')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_barcode_histories');
    }
};
