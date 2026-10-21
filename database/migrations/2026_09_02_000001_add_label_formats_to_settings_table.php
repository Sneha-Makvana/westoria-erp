<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'label_formats')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->json('label_formats')->nullable()->after('barcode_detail_option');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'label_formats')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('label_formats');
            });
        }
    }
};
