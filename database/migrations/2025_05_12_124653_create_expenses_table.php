<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->integer('sr_no')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('expense_name');
            $table->date('expense_date');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('expense_type_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();

            $table->foreign('expense_type_id')->references('id')->on('expense_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_types');
    }
};
