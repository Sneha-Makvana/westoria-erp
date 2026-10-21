<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'show_hr_dashboard')) {
                $table->boolean('show_hr_dashboard')->default(true)->after('show_crm_dashboard');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'show_hr_dashboard')) {
                $table->dropColumn('show_hr_dashboard');
            }
        });
    }
};
