<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'show_erp_products_delivery')) {
                $table->boolean('show_erp_products_delivery')->default(true)->after('show_erp_recent_products');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'show_erp_products_delivery')) {
                $table->dropColumn('show_erp_products_delivery');
            }
        });
    }
};
