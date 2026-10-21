<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'barcode_setting_type')) {
                $table->string('barcode_setting_type')->nullable()->after('payment_methods');
            }

            if (! Schema::hasColumn('settings', 'barcode_detail_option')) {
                $table->string('barcode_detail_option')->nullable()->after('barcode_setting_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'barcode_detail_option')) {
                $table->dropColumn('barcode_detail_option');
            }

            if (Schema::hasColumn('settings', 'barcode_setting_type')) {
                $table->dropColumn('barcode_setting_type');
            }
        });
    }
};
