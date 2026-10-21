<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'import_supplier')) {
                $table->string('import_supplier')->nullable();
            }
            if (! Schema::hasColumn('orders', 'import_ref_doc_no')) {
                $table->string('import_ref_doc_no')->nullable();
            }
            if (! Schema::hasColumn('orders', 'import_ref_doc_date')) {
                $table->date('import_ref_doc_date')->nullable()->after('import_ref_doc_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'import_ref_doc_date')) {
                $table->dropColumn('import_ref_doc_date');
            }
            if (Schema::hasColumn('orders', 'import_ref_doc_no')) {
                $table->dropColumn('import_ref_doc_no');
            }
            if (Schema::hasColumn('orders', 'import_supplier')) {
                $table->dropColumn('import_supplier');
            }
        });
    }
};
