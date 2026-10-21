<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('initial_stock', 15, 3)->nullable()->default(0);
            $table->decimal('current_stock', 15, 3)->nullable()->default(0);
            $table->string('type')->nullable();
            $table->unsignedBigInteger('create_by')->nullable();
            $table->date('date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_inventory');
    }
};
