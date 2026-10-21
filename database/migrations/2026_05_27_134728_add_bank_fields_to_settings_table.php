<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {

            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('ac_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('qr_code')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {

            $table->dropColumn([
                'bank_name',
                'branch',
                'ac_no',
                'ifsc_code',
                'qr_code'
            ]);

        });
    }
};
