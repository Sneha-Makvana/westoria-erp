<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const MODULES = [
        34 => 'Sales Report',
        35 => 'Purchase Report',
        36 => 'Expense Report',
        37 => 'Profit Loss Report',
        38 => 'TDS Report',
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
                    'module'     => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE modules AUTO_INCREMENT = 39');
        }
    }

    public function down(): void
    {
        DB::table('modules')->whereIn('id', array_keys(self::MODULES))->delete();
    }
};
