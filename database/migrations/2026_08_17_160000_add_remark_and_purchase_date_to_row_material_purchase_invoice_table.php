<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('row_material_purchase_invoice', function (Blueprint $table) {
            $table->date('purchase_date')->nullable()->after('bill_no');
            $table->text('remark')->nullable()->after('purchase_date');
        });
    }

    public function down(): void
    {
        Schema::table('row_material_purchase_invoice', function (Blueprint $table) {
            $table->dropColumn(['purchase_date', 'remark']);
        });
    }
};
