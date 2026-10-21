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
        Schema::table('account_branches', function (Blueprint $table) {
            $table->string('branch_code')->nullable()->after('name');
            $table->string('branch_company_name')->nullable()->after('branch_code');
            $table->string('address_line_2')->nullable()->after('address');
            $table->string('address_line_3')->nullable()->after('address_line_2');
            $table->string('phone_2')->nullable()->after('phone');
            $table->string('tin')->nullable()->after('email');
            $table->string('gstin')->nullable()->after('tin');
            $table->string('pan')->nullable()->after('gstin');
            $table->string('area')->nullable()->after('city');
            $table->date('opened_on')->nullable()->after('zip_code');
            $table->date('closed_on')->nullable()->after('opened_on');
            $table->string('branch_type')->nullable()->after('closed_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_branches', function (Blueprint $table) {
            $table->dropColumn([
                'branch_code',
                'branch_company_name',
                'address_line_2',
                'address_line_3',
                'phone_2',
                'tin',
                'gstin',
                'pan',
                'area',
                'opened_on',
                'closed_on',
                'branch_type'
            ]);
        });
    }
};
