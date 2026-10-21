<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoice', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_invoice', 'import_purchase_type')) {
                $table->string('import_purchase_type')->nullable()->after('import_status');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (! Schema::hasColumn('purchases', 'free_quantity')) {
                $table->decimal('free_quantity', 15, 3)->default(0)->after('quantity');
            }
            if (! Schema::hasColumn('purchases', 'imei_no')) {
                $table->string('imei_no')->nullable()->after('free_quantity');
            }
            if (! Schema::hasColumn('purchases', 'activation_date')) {
                $table->date('activation_date')->nullable()->after('imei_no');
            }
            if (! Schema::hasColumn('purchases', 'taxable_amount')) {
                $table->decimal('taxable_amount', 12, 2)->default(0)->after('discount_percent');
            }
            if (! Schema::hasColumn('purchases', 'cgst_amount')) {
                $table->decimal('cgst_amount', 12, 2)->default(0)->after('taxable_amount');
            }
            if (! Schema::hasColumn('purchases', 'sgst_amount')) {
                $table->decimal('sgst_amount', 12, 2)->default(0)->after('cgst_amount');
            }
            if (! Schema::hasColumn('purchases', 'igst_amount')) {
                $table->decimal('igst_amount', 12, 2)->default(0)->after('sgst_amount');
            }
            if (! Schema::hasColumn('purchases', 'net_amount')) {
                $table->decimal('net_amount', 12, 2)->default(0)->after('igst_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_invoice', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_invoice', 'import_purchase_type')) {
                $table->dropColumn('import_purchase_type');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            $columns = [
                'free_quantity',
                'imei_no',
                'activation_date',
                'taxable_amount',
                'cgst_amount',
                'sgst_amount',
                'igst_amount',
                'net_amount',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('purchases', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
