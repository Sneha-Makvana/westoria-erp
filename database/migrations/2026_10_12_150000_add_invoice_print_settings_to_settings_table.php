<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'invoice_print_settings')) {
                $table->json('invoice_print_settings')->nullable()->after('invoice_customization');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'invoice_print_settings')) {
                $table->dropColumn('invoice_print_settings');
            }
        });
    }
};
