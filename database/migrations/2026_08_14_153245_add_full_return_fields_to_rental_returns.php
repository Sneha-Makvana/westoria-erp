<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add is_full_return and deposit_settled to rental_returns
        if (Schema::hasTable('rental_returns')) {
            Schema::table('rental_returns', function (Blueprint $table) {
                if (!Schema::hasColumn('rental_returns', 'is_full_return')) {
                    $table->tinyInteger('is_full_return')->default(0)->after('remarks')
                          ->comment('1 = all items fully returned in this return batch');
                }
                if (!Schema::hasColumn('rental_returns', 'deposit_settled')) {
                    $table->tinyInteger('deposit_settled')->default(0)->after('is_full_return')
                          ->comment('1 = deposit was applied/settled at return time');
                }
                if (!Schema::hasColumn('rental_returns', 'deposit_amount_applied')) {
                    $table->decimal('deposit_amount_applied', 15, 2)->default(0)->after('deposit_settled')
                          ->comment('Deposit amount credited against outstanding on full return');
                }
            });
        }

        // Add deposit_status to orders table
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'deposit_status')) {
                    $table->string('deposit_status')->default('held')->after('deposit_amount')
                          ->comment('held | settled | refunded');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rental_returns')) {
            Schema::table('rental_returns', function (Blueprint $table) {
                $table->dropColumn(['is_full_return', 'deposit_settled', 'deposit_amount_applied']);
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('deposit_status');
            });
        }
    }
};
