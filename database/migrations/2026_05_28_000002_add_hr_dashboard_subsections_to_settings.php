<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Top 4 metric boxes
            if (! Schema::hasColumn('settings', 'show_hr_staff_strength')) {
                $table->boolean('show_hr_staff_strength')->default(true)->after('show_crm_next_7_days');
            }
            if (! Schema::hasColumn('settings', 'show_hr_active_staff')) {
                $table->boolean('show_hr_active_staff')->default(true)->after('show_hr_staff_strength');
            }
            if (! Schema::hasColumn('settings', 'show_hr_monthly_attendance')) {
                $table->boolean('show_hr_monthly_attendance')->default(true)->after('show_hr_active_staff');
            }
            if (! Schema::hasColumn('settings', 'show_hr_personal_progress')) {
                $table->boolean('show_hr_personal_progress')->default(true)->after('show_hr_monthly_attendance');
            }
            
            // Charts and sections
            if (! Schema::hasColumn('settings', 'show_hr_attendance_pattern')) {
                $table->boolean('show_hr_attendance_pattern')->default(true)->after('show_hr_personal_progress');
            }
            if (! Schema::hasColumn('settings', 'show_hr_salary_payroll_trend')) {
                $table->boolean('show_hr_salary_payroll_trend')->default(true)->after('show_hr_attendance_pattern');
            }
            if (! Schema::hasColumn('settings', 'show_hr_payroll_snapshot')) {
                $table->boolean('show_hr_payroll_snapshot')->default(true)->after('show_hr_salary_payroll_trend');
            }
            if (! Schema::hasColumn('settings', 'show_hr_attendance_watch')) {
                $table->boolean('show_hr_attendance_watch')->default(true)->after('show_hr_payroll_snapshot');
            }
            if (! Schema::hasColumn('settings', 'show_hr_payroll_status')) {
                $table->boolean('show_hr_payroll_status')->default(true)->after('show_hr_attendance_watch');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $columns = [
                'show_hr_staff_strength',
                'show_hr_active_staff',
                'show_hr_monthly_attendance',
                'show_hr_personal_progress',
                'show_hr_attendance_pattern',
                'show_hr_salary_payroll_trend',
                'show_hr_payroll_snapshot',
                'show_hr_attendance_watch',
                'show_hr_payroll_status',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
