<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->longText('terms_condition_eng')->nullable()->after('address');
            $table->longText('terms_condition_guj')->nullable()->after('terms_condition_eng');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['terms_condition_eng', 'terms_condition_guj']);
        });
    }
};
