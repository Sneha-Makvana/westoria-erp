<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_deposits')) {
            Schema::create('customer_deposits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('entry_type', 20); // credit | debit
                $table->decimal('amount', 15, 2);
                $table->decimal('balance_after', 15, 2)->default(0);
                $table->string('source', 40)->nullable(); // exchange_surplus | bill_payment | adjustment
                $table->string('notes', 255)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['customer_id', 'branch_id']);
                $table->index('order_id');
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'exchange_surplus')) {
                $table->decimal('exchange_surplus', 15, 2)->nullable()->default(0)->after('exchange_amount');
            }
            if (!Schema::hasColumn('orders', 'exchange_surplus_action')) {
                $table->string('exchange_surplus_action', 30)->nullable()->after('exchange_surplus');
            }
            if (!Schema::hasColumn('orders', 'customer_deposit_applied')) {
                $table->decimal('customer_deposit_applied', 15, 2)->nullable()->default(0)->after('deposit_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_deposit_applied')) {
                $table->dropColumn('customer_deposit_applied');
            }
            if (Schema::hasColumn('orders', 'exchange_surplus_action')) {
                $table->dropColumn('exchange_surplus_action');
            }
            if (Schema::hasColumn('orders', 'exchange_surplus')) {
                $table->dropColumn('exchange_surplus');
            }
        });

        Schema::dropIfExists('customer_deposits');
    }
};
