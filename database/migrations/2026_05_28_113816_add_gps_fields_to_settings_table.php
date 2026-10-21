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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'office_latitude')) {
                $table->decimal('office_latitude', 10, 7)->nullable()->after('close_time');
            }
            if (!Schema::hasColumn('settings', 'office_longitude')) {
                $table->decimal('office_longitude', 10, 7)->nullable()->after('office_latitude');
            }
            if (!Schema::hasColumn('settings', 'office_radius')) {
                $table->unsignedInteger('office_radius')->nullable()->default(200)->after('office_longitude');
            }
            if (!Schema::hasColumn('settings', 'location_check_enabled')) {
                $table->tinyInteger('location_check_enabled')->default(0)->after('office_radius');
            }
            if (!Schema::hasColumn('settings', 'overtime_after_hours')) {
                $table->decimal('overtime_after_hours', 5, 2)->nullable()->after('location_check_enabled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'office_latitude',
                'office_longitude',
                'office_radius',
                'location_check_enabled',
                'overtime_after_hours',
            ]);
        });
    }
};
