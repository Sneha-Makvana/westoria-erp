<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * All role names used across the application.
     *
     * These slugs are stored in `users.role` and also seeded into the
     * Spatie `roles` table so they can be assigned via the permission system.
     *
     * Roles reference:
     *  - admin            : Full system access; manages all branches, users, and settings.
     *  - sub-admin        : Branch owner with full access to their own branch data.
     *  - staff            : Limited access; tied to a branch, cannot access admin settings.
     *  - sales-manager    : Access to sales, orders, and customer management.
     *  - purchase-manager : Access to purchases, vendors, and purchase returns.
     *  - inventory-manager: Access to products, stock, and inventory adjustments.
     *  - customer         : Customer record used for sales and invoicing.
     *  - vendor           : Vendor/supplier record used for purchases and bills.
     */
    public const ROLES = [
        'admin',
        'sub-admin',
        'staff',
        'sales-manager',
        'purchase-manager',
        'inventory-manager',
        'customer',
        'vendor',
    ];

    /**
     * Seed all application roles into the Spatie roles table.
     *
     * Uses firstOrCreate so re-running the seeder is safe (no duplicates).
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::ROLES as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }

        $this->command->info('Roles seeded: ' . implode(', ', self::ROLES));
    }
}
