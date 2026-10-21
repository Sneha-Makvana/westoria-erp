<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Matches the credit_note_items table structure exactly as shown in phpMyAdmin.
     *
     * Columns (in order):
     *  1.  id               – bigint unsigned, AUTO_INCREMENT, PK
     *  2.  branch_id        – bigint unsigned, nullable
     *  3.  transaction_type – varchar(255), nullable
     *  4.  credite_note_id  – bigint unsigned, nullable (FK → credit_notes_type.id)
     *  5.  type_id          – bigint unsigned, nullable
     *  6.  order_id         – bigint unsigned, nullable
     *  7.  purchase_id      – bigint unsigned, nullable
     *  8.  user_id          – bigint unsigned, nullable (FK → users.id)
     *  9.  total_amt        – decimal(12,2), default 0.00
     *  10. remaining_amt    – decimal(12,2), default 0.00
     *  11. total_paid       – decimal(12,2), default 0.00
     *  12. settlement_amount– decimal(12,2), default 0.00
     *  13. reason           – text, nullable
     *  14. total            – decimal(12,2), default 0.00
     *  15. isDeleted        – tinyint(1), default 0
     *  16. created_at       – timestamp, nullable
     *  17. updated_at       – timestamp, nullable
     */
    public function up(): void
    {
        if (Schema::hasTable('credit_note_items')) {
            $columns = [
                'branch_id' => fn (Blueprint $table) => $table->unsignedBigInteger('branch_id')->nullable()->after('id'),
                'transaction_type' => fn (Blueprint $table) => $table->string('transaction_type')->nullable()->after('branch_id'),
                'credite_note_id' => fn (Blueprint $table) => $table->unsignedBigInteger('credite_note_id')->nullable()->after('transaction_type'),
                'type_id' => fn (Blueprint $table) => $table->unsignedBigInteger('type_id')->nullable()->after('credite_note_id'),
                'order_id' => fn (Blueprint $table) => $table->unsignedBigInteger('order_id')->nullable()->after('type_id'),
                'purchase_id' => fn (Blueprint $table) => $table->unsignedBigInteger('purchase_id')->nullable()->after('order_id'),
                'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable()->after('purchase_id'),
                'total_amt' => fn (Blueprint $table) => $table->decimal('total_amt', 12, 2)->default(0.00)->after('user_id'),
                'remaining_amt' => fn (Blueprint $table) => $table->decimal('remaining_amt', 12, 2)->default(0.00)->after('total_amt'),
                'total_paid' => fn (Blueprint $table) => $table->decimal('total_paid', 12, 2)->default(0.00)->after('remaining_amt'),
                'settlement_amount' => fn (Blueprint $table) => $table->decimal('settlement_amount', 12, 2)->default(0.00)->after('total_paid'),
                'reason' => fn (Blueprint $table) => $table->text('reason')->nullable()->after('settlement_amount'),
                'total' => fn (Blueprint $table) => $table->decimal('total', 12, 2)->default(0.00)->after('reason'),
                'isDeleted' => fn (Blueprint $table) => $table->tinyInteger('isDeleted')->default(0)->after('total'),
                'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable()->after('isDeleted'),
                'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable()->after('created_at'),
            ];

            foreach ($columns as $column => $addColumn) {
                if (! Schema::hasColumn('credit_note_items', $column)) {
                    Schema::table('credit_note_items', $addColumn);
                }
            }

            return;
        }

        Schema::create('credit_note_items', function (Blueprint $table) {
            $table->id();                                                        // col 1  – bigint unsigned AUTO_INCREMENT PK

            $table->unsignedBigInteger('branch_id')->nullable();                 // col 2

            $table->string('transaction_type')->nullable();                      // col 3

            $table->unsignedBigInteger('credite_note_id')->nullable();           // col 4
   

            $table->unsignedBigInteger('type_id')->nullable();                   // col 5

            $table->unsignedBigInteger('order_id')->nullable();                  // col 6

            $table->unsignedBigInteger('purchase_id')->nullable();               // col 7

            $table->unsignedBigInteger('user_id')->nullable();                   // col 8


            $table->decimal('total_amt', 12, 2)->default(0.00);                  // col 9
            $table->decimal('remaining_amt', 12, 2)->default(0.00);              // col 10
            $table->decimal('total_paid', 12, 2)->default(0.00);                 // col 11
            $table->decimal('settlement_amount', 12, 2)->default(0.00);          // col 12

            $table->text('reason')->nullable();                                  // col 13

            $table->decimal('total', 12, 2)->default(0.00);                      // col 14

            $table->tinyInteger('isDeleted')->default(0);                        // col 15

            $table->timestamp('created_at')->nullable();                         // col 16
            $table->timestamp('updated_at')->nullable();                         // col 17
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_note_items');
    }
};
