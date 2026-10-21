<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const MODULES = [
        6 => 'Catalog Setup',
        8 => 'Staff',
        15 => 'Tax',
        16 => 'Accounting',
        17 => 'Inventory',
        20 => 'GST Reports',
        23 => 'Advance Pay',
        28 => 'Leaves',
        29 => 'Payroll',
    ];

    public function up(): void
    {
        foreach (self::MODULES as $id => $name) {
            DB::table('modules')
                ->where('module', $name)
                ->where('id', '!=', $id)
                ->delete();

            DB::table('modules')->updateOrInsert(
                ['id' => $id],
                [
                    'module' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('modules')
            ->whereIn('id', array_keys(self::MODULES))
            ->delete();
    }
};
