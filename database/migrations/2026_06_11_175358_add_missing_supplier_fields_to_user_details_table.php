<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            if (!Schema::hasColumn('user_details', 'supplier_since')) $table->date('supplier_since')->nullable();
            if (!Schema::hasColumn('user_details', 'address_line2')) $table->string('address_line2')->nullable();
            if (!Schema::hasColumn('user_details', 'address_line3')) $table->string('address_line3')->nullable();
            if (!Schema::hasColumn('user_details', 'pin_code')) $table->string('pin_code')->nullable();
            if (!Schema::hasColumn('user_details', 'phone_2')) $table->string('phone_2')->nullable();
            if (!Schema::hasColumn('user_details', 'supplier_category')) $table->string('supplier_category')->nullable();
            if (!Schema::hasColumn('user_details', 'tin_number')) $table->string('tin_number')->nullable();
            if (!Schema::hasColumn('user_details', 'credit_days')) $table->integer('credit_days')->nullable();
            if (!Schema::hasColumn('user_details', 'pan_status')) $table->string('pan_status')->nullable();
            if (!Schema::hasColumn('user_details', 'gstin_status')) $table->string('gstin_status')->nullable();
            if (!Schema::hasColumn('user_details', 'account_group')) $table->string('account_group')->nullable();
            if (!Schema::hasColumn('user_details', 'tan_status')) $table->string('tan_status')->nullable();
            if (!Schema::hasColumn('user_details', 'tan_number')) $table->string('tan_number')->nullable();
            if (!Schema::hasColumn('user_details', 'msme_status')) $table->string('msme_status')->nullable();
            if (!Schema::hasColumn('user_details', 'msme_number')) $table->string('msme_number')->nullable();
            if (!Schema::hasColumn('user_details', 'status')) $table->string('status')->nullable()->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $columnsToDrop = [];
            $allColumns = [
                'supplier_since', 'address_line2', 'address_line3', 'pin_code', 
                'phone_2', 'supplier_category', 'tin_number', 'credit_days', 
                'pan_status', 'gstin_status', 'account_group', 'tan_status', 
                'tan_number', 'msme_status', 'msme_number', 'status'
            ];
            foreach ($allColumns as $col) {
                if (Schema::hasColumn('user_details', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
