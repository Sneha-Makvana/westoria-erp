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
        // Change the status column from tinyint(1) to varchar(20)
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `row_material` MODIFY COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to tinyint(1)
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `row_material` MODIFY COLUMN `status` TINYINT(1) NOT NULL DEFAULT 1");
    }
};
