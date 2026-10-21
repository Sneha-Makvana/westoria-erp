<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds enable_variant_product column to settings table.
     * When 1 (On)  – both Normal Product & Variant Product radio buttons are shown.
     * When 0 (Off) – only Normal Product is shown; Variant Product option is hidden.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'enable_variant_product')) {
                $table->boolean('enable_variant_product')->default(1)->after('rental');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'enable_variant_product')) {
                $table->dropColumn('enable_variant_product');
            }
        });
    }
};
