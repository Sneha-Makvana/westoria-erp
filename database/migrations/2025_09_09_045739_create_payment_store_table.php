<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_store', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('custom_invoice_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->decimal('payment_amount', 12, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('cash_amount', 12, 2)->nullable()->default(0);
            $table->decimal('upi_amount', 12, 2)->nullable()->default(0);
            $table->string('emi_month')->nullable();
            $table->decimal('remaining_amount', 12, 2)->nullable()->default(0);
            $table->string('status')->nullable()->default('paid');
            $table->text('remarks')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('custom_invoice_id')->references('id')->on('custom_invoice')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_store');
    }
};
