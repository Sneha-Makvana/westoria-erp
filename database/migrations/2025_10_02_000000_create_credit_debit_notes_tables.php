<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Credit note types (categories of credit notes)
        Schema::create('credit_notes_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('type_name');
            $table->boolean('isdeleted')->default(0);
            $table->timestamps();
        });

        // Credit note items (individual credit note transactions)
        Schema::create('credit_note_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('credite_note_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('total_amt', 12, 2)->default(0);
            $table->decimal('remaining_amt', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('settlement_amount', 12, 2)->default(0);
            $table->text('reason')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('isDeleted')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('credite_note_id')->references('id')->on('credit_notes_type')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Debit notes
        Schema::create('debit_notes_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('transaction_type')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('create_note_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('remaning_amount', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('settlement_amount', 12, 2)->default(0);
            $table->text('reason')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('isDeleted')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debit_notes_type');
        Schema::dropIfExists('credit_note_items');
        Schema::dropIfExists('credit_notes_type');
    }
};
