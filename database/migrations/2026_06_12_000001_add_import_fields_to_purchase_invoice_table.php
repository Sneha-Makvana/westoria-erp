<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_invoice', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_invoice', 'import_sn')) {
                $table->unsignedInteger('import_sn')->nullable()->after('purchase_date');
            }
            if (! Schema::hasColumn('purchase_invoice', 'import_supplier')) {
                $table->string('import_supplier')->nullable()->after('import_sn');
            }
            if (! Schema::hasColumn('purchase_invoice', 'import_doc_no')) {
                $table->string('import_doc_no')->nullable()->after('import_supplier');
            }
            if (! Schema::hasColumn('purchase_invoice', 'import_doc_date')) {
                $table->date('import_doc_date')->nullable()->after('import_doc_no');
            }
            if (! Schema::hasColumn('purchase_invoice', 'import_ref_doc_no')) {
                $table->string('import_ref_doc_no')->nullable()->after('import_doc_date');
            }
            if (! Schema::hasColumn('purchase_invoice', 'import_ref_doc_date')) {
                $table->date('import_ref_doc_date')->nullable()->after('import_ref_doc_no');
            }
            if (! Schema::hasColumn('purchase_invoice', 'import_doc_value')) {
                $table->decimal('import_doc_value', 12, 2)->nullable()->default(0)->after('import_ref_doc_date');
            }
            if (! Schema::hasColumn('purchase_invoice', 'import_status')) {
                $table->string('import_status')->nullable()->after('import_doc_value');
            }
            if (! Schema::hasColumn('purchase_invoice', 'import_source')) {
                $table->string('import_source')->nullable()->after('import_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_invoice', function (Blueprint $table) {
            $columns = [
                'import_sn',
                'import_supplier',
                'import_doc_no',
                'import_doc_date',
                'import_ref_doc_no',
                'import_ref_doc_date',
                'import_doc_value',
                'import_status',
                'import_source',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('purchase_invoice', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
