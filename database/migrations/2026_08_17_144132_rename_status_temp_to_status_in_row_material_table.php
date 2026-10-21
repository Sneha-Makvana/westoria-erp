<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Directly alter the status column to VARCHAR(20)
        DB::statement("ALTER TABLE `row_material` MODIFY COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'active'");

        // Safety: fill any remaining NULLs with 'active'
        DB::table('row_material')->whereNull('status')->update(['status' => 'active']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `row_material` CHANGE COLUMN `status` `status` TINYINT(1) NOT NULL DEFAULT 1");
    }
};