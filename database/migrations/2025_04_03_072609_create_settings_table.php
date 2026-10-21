<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('state_code')->nullable();
            $table->string('gst_num')->nullable();
            $table->string('cin_no')->nullable();
            $table->integer('low_stock')->default(5);
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('currency_position')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('invoice_size')->nullable();
            // Attendance settings
            $table->string('working_hours')->nullable();
            $table->boolean('sunday_off')->default(1);
            $table->integer('grace_period')->nullable();
            $table->string('lunch_break')->nullable();
            $table->string('open_time')->nullable();
            $table->string('close_time')->nullable();
            // Feature flags
            $table->boolean('send_mail')->default(true);
            $table->boolean('financial_year')->default(true);
            $table->boolean('tds_apply')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
