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
        Schema::table('custom_invoice_item', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['item']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_invoice_item', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('custom_invoice')->onDelete('set null');
            $table->foreign('item')->references('id')->on('products')->onDelete('set null');
        });
    }
};
