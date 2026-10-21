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
        Schema::table('holiday_calendar', function (Blueprint $table) {
            $table->string('holiday_name')->nullable()->change();
            $table->date('date')->nullable()->change();
            $table->string('type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holiday_calendar', function (Blueprint $table) {
            $table->string('holiday_name')->nullable(false)->change();
            $table->date('date')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
        });
    }
};
