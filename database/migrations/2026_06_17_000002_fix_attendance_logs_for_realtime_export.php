<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix AttendanceLogs column types for Realtime Parallel Database Export.
     * Realtime maps EmployeeCode as Int — nullable VARCHAR causes DBNull cast errors.
     */
    public function up(): void
    {
        if (! Schema::hasTable('AttendanceLogs')) {
            return;
        }

        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `EmployeeID` INT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `EmployeeCode` INT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `LogDateTime` DATETIME NOT NULL DEFAULT \'1970-01-01 00:00:00\'');
        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `LogDate` DATE NULL');
        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `LogTime` TIME NULL');
        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `Direction` VARCHAR(20) NOT NULL DEFAULT \'\'');
        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `DeviceSerialNumber` VARCHAR(100) NOT NULL DEFAULT \'\'');
        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `DeviceipAddress` VARCHAR(50) NOT NULL DEFAULT \'\'');
        DB::statement('ALTER TABLE `AttendanceLogs` MODIFY `DeviceId` VARCHAR(50) NOT NULL DEFAULT \'\'');
    }

    public function down(): void
    {
        // No rollback — types are corrected for production use.
    }
};
