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
            // Add new sales setting fields
            if (!Schema::hasColumn('settings', 'show_assign_staff')) {
                $table->boolean('show_assign_staff')->default(true)->after('rental');
            }
            if (!Schema::hasColumn('settings', 'show_order_type')) {
                $table->boolean('show_order_type')->default(true)->after('show_assign_staff');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'show_assign_staff')) {
                $table->dropColumn('show_assign_staff');
            }
            if (Schema::hasColumn('settings', 'show_order_type')) {
                $table->dropColumn('show_order_type');
            }
        });
    }
};
