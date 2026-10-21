<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('emi_down_payment', 12, 2)->nullable()->default(0)->after('remaining_amount');
            $table->decimal('emi_loan_amount', 12, 2)->nullable()->default(0)->after('emi_down_payment');
            $table->decimal('emi_interest_rate', 5, 2)->nullable()->default(0)->after('emi_loan_amount');
            $table->string('emi_tenure')->nullable()->after('emi_interest_rate');
            $table->decimal('emi_monthly_amount', 12, 2)->nullable()->default(0)->after('emi_tenure');
            $table->string('emi_aadhar_number')->nullable()->after('emi_monthly_amount');
            $table->string('emi_pan_number')->nullable()->after('emi_aadhar_number');
            $table->string('emi_guarantor_name')->nullable()->after('emi_pan_number');
        });

        Schema::table('payment_store', function (Blueprint $table) {
            $table->decimal('emi_down_payment', 12, 2)->nullable()->default(0)->after('upi_amount');
            $table->decimal('emi_loan_amount', 12, 2)->nullable()->default(0)->after('emi_down_payment');
            $table->decimal('emi_interest_rate', 5, 2)->nullable()->default(0)->after('emi_loan_amount');
            $table->string('emi_tenure')->nullable()->after('emi_interest_rate');
            $table->decimal('emi_monthly_amount', 12, 2)->nullable()->default(0)->after('emi_tenure');
            $table->string('emi_aadhar_number')->nullable()->after('emi_monthly_amount');
            $table->string('emi_pan_number')->nullable()->after('emi_aadhar_number');
            $table->string('emi_guarantor_name')->nullable()->after('emi_pan_number');
        });
    }

    public function down(): void
    {
        Schema::table('payment_store', function (Blueprint $table) {
            $table->dropColumn([
                'emi_down_payment',
                'emi_loan_amount',
                'emi_interest_rate',
                'emi_tenure',
                'emi_monthly_amount',
                'emi_aadhar_number',
                'emi_pan_number',
                'emi_guarantor_name',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'emi_down_payment',
                'emi_loan_amount',
                'emi_interest_rate',
                'emi_tenure',
                'emi_monthly_amount',
                'emi_aadhar_number',
                'emi_pan_number',
                'emi_guarantor_name',
            ]);
        });
    }
};
