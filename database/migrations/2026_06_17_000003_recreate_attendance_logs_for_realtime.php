<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Realtime Parallel Export expects ONLY its mapped columns — no id/imported_at.
     * Extra nullable columns cause: "Object cannot be cast from DBNull to other types".
     */
    public function up(): void
    {
        Schema::dropIfExists('AttendanceLogs');

        DB::statement("
            CREATE TABLE `AttendanceLogs` (
                `EmployeeID` INT NOT NULL DEFAULT 0,
                `EmployeeCode` INT NOT NULL DEFAULT 0,
                `LogDateTime` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
                `LogDate` DATE NOT NULL DEFAULT '1970-01-01',
                `LogTime` TIME NOT NULL DEFAULT '00:00:00',
                `Direction` VARCHAR(20) NOT NULL DEFAULT '',
                `DeviceSerialNumber` VARCHAR(100) NOT NULL DEFAULT '',
                `DeviceipAddress` VARCHAR(50) NOT NULL DEFAULT '',
                KEY `idx_attendance_logs_datetime` (`LogDateTime`),
                KEY `idx_attendance_logs_employee` (`EmployeeID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        if (! Schema::hasTable('biometric_realtime_imported_keys')) {
            Schema::create('biometric_realtime_imported_keys', function (Blueprint $table) {
                $table->id();
                $table->integer('employee_id')->default(0);
                $table->dateTime('log_datetime');
                $table->string('direction', 20)->default('');
                $table->timestamp('imported_at')->useCurrent();

                $table->unique(
                    ['employee_id', 'log_datetime', 'direction'],
                    'biometric_realtime_imported_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('biometric_realtime_imported_keys');
        Schema::dropIfExists('AttendanceLogs');
    }
};
