<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('credit_note_items') || Schema::hasColumn('credit_note_items', 'transaction_type')) {
            return;
        }

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->string('transaction_type')->nullable()->after('branch_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('credit_note_items') || ! Schema::hasColumn('credit_note_items', 'transaction_type')) {
            return;
        }

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
};
