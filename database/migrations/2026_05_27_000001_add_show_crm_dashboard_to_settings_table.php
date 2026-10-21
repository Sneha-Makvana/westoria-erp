<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'show_crm_dashboard')) {
                $table->boolean('show_crm_dashboard')->default(true)->after('tds_apply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'show_crm_dashboard')) {
                $table->dropColumn('show_crm_dashboard');
            }
        });
    }
};
