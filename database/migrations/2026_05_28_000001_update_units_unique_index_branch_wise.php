<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Drop old global unique key on unit_name if present.
            try {
                $table->dropUnique('units_unit_name_unique');
            } catch (\Throwable $e) {
                // Ignore when key does not exist.
            }
            $table->unique(['created_by', 'unit_name'], 'units_created_by_unit_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropUnique('units_created_by_unit_name_unique');
            $table->unique('unit_name', 'units_unit_name_unique');
        });
    }
};
