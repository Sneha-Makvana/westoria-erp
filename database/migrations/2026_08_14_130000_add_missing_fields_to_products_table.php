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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'mrp')) {
                $table->decimal('mrp', 15, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'batch_no')) {
                $table->string('batch_no')->nullable()->after('mrp');
            }
            if (!Schema::hasColumn('products', 'label_code')) {
                $table->string('label_code')->nullable()->after('batch_no');
            }
            if (!Schema::hasColumn('products', 'label_reference')) {
                $table->string('label_reference')->nullable()->after('label_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('products', 'mrp')) {
                $columnsToDrop[] = 'mrp';
            }
            if (Schema::hasColumn('products', 'batch_no')) {
                $columnsToDrop[] = 'batch_no';
            }
            if (Schema::hasColumn('products', 'label_code')) {
                $columnsToDrop[] = 'label_code';
            }
            if (Schema::hasColumn('products', 'label_reference')) {
                $columnsToDrop[] = 'label_reference';
            }
            if (count($columnsToDrop) > 0) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
