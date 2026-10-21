<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biometric_punches', function (Blueprint $table) {
            $table->id();
            $table->string('device_serial')->nullable()->index();
            $table->string('device_user_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('employee_name')->nullable();
            $table->dateTime('punch_time')->index();
            $table->date('punch_date')->index();
            $table->enum('punch_type', ['check_in', 'check_out', 'unknown'])->default('unknown');
            $table->unsignedTinyInteger('verify_mode')->nullable();
            $table->unsignedTinyInteger('device_state')->nullable();
            $table->string('device_ip')->nullable();
            $table->string('source')->default('push');
            $table->string('raw_payload', 500)->nullable();
            $table->timestamps();

            $table->unique(
                ['device_serial', 'device_user_id', 'punch_time', 'device_state'],
                'biometric_punches_unique_punch'
            );
        });

        if (Schema::hasTable('user_details') && ! Schema::hasColumn('user_details', 'biometric_enroll_id')) {
            Schema::table('user_details', function (Blueprint $table) {
                $table->string('biometric_enroll_id')->nullable()->after('user_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_details') && Schema::hasColumn('user_details', 'biometric_enroll_id')) {
            Schema::table('user_details', function (Blueprint $table) {
                $table->dropColumn('biometric_enroll_id');
            });
        }

        Schema::dropIfExists('biometric_punches');
    }
};
