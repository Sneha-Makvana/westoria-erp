<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table for Realtime Attendance Tracker "Parallel Database Export".
     * Column names match Realtime software field mapping exactly.
     */
    public function up(): void
    {
        if (Schema::hasTable('AttendanceLogs')) {
            return;
        }

        Schema::create('AttendanceLogs', function (Blueprint $table) {
            $table->id();
            $table->integer('EmployeeID')->default(0)->index();
            $table->integer('EmployeeCode')->default(0)->index();
            $table->dateTime('LogDateTime')->useCurrent()->index();
            $table->date('LogDate')->nullable()->index();
            $table->time('LogTime')->nullable();
            $table->dateTime('LogDateTime2')->nullable();
            $table->date('LogDate2')->nullable();
            $table->time('LogTime2')->nullable();
            $table->dateTime('DownloadDateTime')->nullable();
            $table->string('Direction', 20)->default('');
            $table->string('DeviceSerialNumber', 100)->default('');
            $table->string('DeviceipAddress', 50)->default('');
            $table->string('DeviceId', 50)->default('');
            $table->timestamp('imported_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('AttendanceLogs');
    }
};
