<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_ins')) {
            return;
        }

        Schema::create('payment_ins', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 20)->default('cash');
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->date('payment_date')->nullable();
            $table->boolean('settle_old_bills')->default(false);
            $table->decimal('settled_amount', 15, 2)->default(0);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->string('remarks', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_ins');
    }
};
