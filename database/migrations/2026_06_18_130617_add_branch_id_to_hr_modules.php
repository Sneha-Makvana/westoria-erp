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
        if (Schema::hasTable('attendance')) {
            Schema::table('attendance', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
                }
            });
        }
        
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (!Schema::hasColumn('leaves', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
                }
            });
        }
        
        if (Schema::hasTable('payroll')) {
            Schema::table('payroll', function (Blueprint $table) {
                if (!Schema::hasColumn('payroll', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('user_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendance') && Schema::hasColumn('attendance', 'branch_id')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
        
        if (Schema::hasTable('leaves') && Schema::hasColumn('leaves', 'branch_id')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
        
        if (Schema::hasTable('payroll') && Schema::hasColumn('payroll', 'branch_id')) {
            Schema::table('payroll', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
