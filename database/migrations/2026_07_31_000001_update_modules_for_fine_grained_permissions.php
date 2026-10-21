<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete Invoices (4) and Accounting (16)
        DB::table('modules')->whereIn('id', [4, 16])->delete();

        // Add new modules
        $modules = [
            ['id' => 40, 'module' => 'Financers'],
            ['id' => 41, 'module' => 'Manage Inventory'],
            ['id' => 42, 'module' => 'Account Ledger'],
            ['id' => 43, 'module' => 'Income Statement'],
            ['id' => 44, 'module' => 'Balance Sheet'],
            ['id' => 45, 'module' => 'Banks'],
            ['id' => 46, 'module' => 'Receipt and Payment'],
            ['id' => 47, 'module' => 'Cash and Bank Book'],
            ['id' => 48, 'module' => 'Credit and Debit Notes'],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['id' => $module['id']],
                [
                    'module' => $module['module'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('modules')->whereIn('id', range(40, 48))->delete();
        
        DB::table('modules')->updateOrInsert(['id' => 4], ['module' => 'Invoices', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('modules')->updateOrInsert(['id' => 16], ['module' => 'Accounting', 'created_at' => now(), 'updated_at' => now()]);
    }
};
