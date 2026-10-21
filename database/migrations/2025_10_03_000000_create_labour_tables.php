<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Labour items master list (Labour and LabourItem models both use this table)
        Schema::create('labour_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();
        });

        // Sales labour items (linked to orders)
        Schema::create('sales_labour_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('labour_item_id')->nullable();
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('labour_item_id')->references('id')->on('labour_items')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_labour_items');
        Schema::dropIfExists('labour_items');
    }
};
