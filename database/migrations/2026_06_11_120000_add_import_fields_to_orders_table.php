<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'import_sn')) {
                $table->unsignedInteger('import_sn')->nullable()->after('order_number');
            }
            if (! Schema::hasColumn('orders', 'import_party_name')) {
                $table->string('import_party_name')->nullable()->after('import_sn');
            }
            if (! Schema::hasColumn('orders', 'import_doc_no')) {
                $table->string('import_doc_no')->nullable()->after('import_party_name');
            }
            if (! Schema::hasColumn('orders', 'import_doc_date')) {
                $table->date('import_doc_date')->nullable()->after('import_doc_no');
            }
            if (! Schema::hasColumn('orders', 'import_doc_value')) {
                $table->decimal('import_doc_value', 12, 2)->nullable()->default(0)->after('import_doc_date');
            }
            if (! Schema::hasColumn('orders', 'import_financier')) {
                $table->string('import_financier')->nullable()->after('import_doc_value');
            }
            if (! Schema::hasColumn('orders', 'import_status')) {
                $table->string('import_status')->nullable()->after('import_financier');
            }
            if (! Schema::hasColumn('orders', 'import_do_status')) {
                $table->string('import_do_status')->nullable()->after('import_status');
            }
            if (! Schema::hasColumn('orders', 'import_source')) {
                $table->string('import_source')->nullable()->after('import_do_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'import_sn',
                'import_party_name',
                'import_doc_no',
                'import_doc_date',
                'import_doc_value',
                'import_financier',
                'import_status',
                'import_do_status',
                'import_source',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
