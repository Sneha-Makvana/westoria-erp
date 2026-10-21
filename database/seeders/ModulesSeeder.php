<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesSeeder extends Seeder
{
    /**
     * Application modules with fixed IDs (matches modules table seed data).
     *
     * Re-running this seeder is safe: rows are upserted by id and the
     * auto-increment counter is reset to continue after the highest id.
     */
    public const MODULES = [
        ['id' => 1,  'module' => 'Products',         'created_at' => null,                    'updated_at' => null],
        ['id' => 2,  'module' => 'Sales and Orders', 'created_at' => null,                    'updated_at' => null],
        ['id' => 3,  'module' => 'Purchases',        'created_at' => null,                    'updated_at' => null],
        ['id' => 4,  'module' => 'Invoices',         'created_at' => null,                    'updated_at' => null],
        ['id' => 5,  'module' => 'Expenses',         'created_at' => null,                    'updated_at' => null],
        ['id' => 6,  'module' => 'Catalog Setup',    'created_at' => null,                    'updated_at' => null],
        ['id' => 8,  'module' => 'Staff',            'created_at' => null,                    'updated_at' => null],
        ['id' => 9,  'module' => 'Customers',        'created_at' => null,                    'updated_at' => null],
        ['id' => 10, 'module' => 'Vendors',          'created_at' => null,                    'updated_at' => null],
        ['id' => 15, 'module' => 'Tax',              'created_at' => null,                    'updated_at' => null],
        ['id' => 16, 'module' => 'Accounting',       'created_at' => null,                    'updated_at' => null],
        ['id' => 17, 'module' => 'Inventory',        'created_at' => null,                    'updated_at' => null],
        ['id' => 20, 'module' => 'GST Reports',      'created_at' => null,                    'updated_at' => null],
        ['id' => 23, 'module' => 'Advance Pay',      'created_at' => null,                    'updated_at' => null],
        ['id' => 26, 'module' => 'Attendance',       'created_at' => '2025-11-04 10:55:19', 'updated_at' => '2025-11-04 10:55:19'],
        ['id' => 27, 'module' => 'Transaction',      'created_at' => null,                    'updated_at' => null],
        ['id' => 28, 'module' => 'Leaves',           'created_at' => null,                    'updated_at' => null],
        ['id' => 29, 'module' => 'Payroll',          'created_at' => null,                    'updated_at' => null],
        ['id' => 30, 'module' => 'Follow Ups',       'created_at' => null,                    'updated_at' => null],
        ['id' => 31, 'module' => 'Meetings',         'created_at' => null,                    'updated_at' => null],
        ['id' => 32, 'module' => 'Manage Leads',     'created_at' => null,                    'updated_at' => null],
        ['id' => 33, 'module' => 'Tickets',          'created_at' => null,                    'updated_at' => null],
        ['id' => 34, 'module' => 'Sales Report',     'created_at' => null,                    'updated_at' => null],
        ['id' => 35, 'module' => 'Purchase Report',  'created_at' => null,                    'updated_at' => null],
        ['id' => 36, 'module' => 'Expense Report',   'created_at' => null,                    'updated_at' => null],
        ['id' => 37, 'module' => 'Profit Loss Report', 'created_at' => null,                  'updated_at' => null],
        ['id' => 38, 'module' => 'TDS Report',       'created_at' => null,                    'updated_at' => null],
        ['id' => 39, 'module' => 'Products Delivery','created_at' => null,                    'updated_at' => null],
        ['id' => 49, 'module' => 'Manufacturing ERP','created_at' => null,                    'updated_at' => null],
    ];

    private const AUTO_INCREMENT = 50;

    /**
     * Seed modules using fixed ids. Existing rows are updated; missing rows are inserted.
     */
    public function run(): void
    {
        foreach (self::MODULES as $module) {
            DB::table('modules')
                ->where('module', $module['module'])
                ->where('id', '!=', $module['id'])
                ->delete();

            DB::table('modules')->updateOrInsert(
                ['id' => $module['id']],
                [
                    'module'     => $module['module'],
                    'created_at' => $module['created_at'],
                    'updated_at' => $module['updated_at'],
                ]
            );
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE modules AUTO_INCREMENT = ' . self::AUTO_INCREMENT);
        }

        $names = array_column(self::MODULES, 'module');
        $this->command->info('Modules seeded: ' . implode(', ', $names));
    }
}
