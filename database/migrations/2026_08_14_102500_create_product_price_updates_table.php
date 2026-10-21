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
        Schema::create('product_price_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_product_id')->comment('The product ID that triggered the update');
            $table->unsignedBigInteger('target_product_id')->comment('The product ID in the target branch');
            $table->unsignedBigInteger('branch_id')->comment('The branch ID receiving the request');
            $table->decimal('old_price', 15, 2)->nullable();
            $table->decimal('new_price', 15, 2);
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->unsignedBigInteger('resolved_by')->nullable()->comment('User who accepted or rejected');
            $table->timestamps();

            $table->foreign('source_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('target_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_updates');
    }
};
