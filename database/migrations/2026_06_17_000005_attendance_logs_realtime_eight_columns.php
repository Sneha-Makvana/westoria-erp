<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exact 8-column schema matching Realtime Parallel Export checked fields.
     */
    public function up(): void
    {
        Schema::dropIfExists('AttendanceLogs');

        DB::statement("
            CREATE TABLE `AttendanceLogs` (
                `EmployeeID` INT NOT NULL DEFAULT 0,
                `EmployeeCode` VARCHAR(50) NOT NULL DEFAULT '',
                `LogDateTime` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
                `LogDate` DATE NOT NULL DEFAULT '1970-01-01',
                `LogTime` TIME NOT NULL DEFAULT '00:00:00',
                `Direction` VARCHAR(20) NOT NULL DEFAULT '',
                `DeviceSerialNumber` VARCHAR(100) NOT NULL DEFAULT '',
                `DeviceipAddress` VARCHAR(50) NOT NULL DEFAULT '',
                KEY `idx_log_datetime` (`LogDateTime`),
                KEY `idx_log_date` (`LogDate`),
                KEY `idx_employee` (`EmployeeID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('AttendanceLogs');
    }
};
