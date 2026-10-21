<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin user and assign the admin Spatie role.
     *
     * Credentials:
     *   Email    : admin@gmail.com
     *   Password : 12345678
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'          => 'Admin',
                'email'         => 'admin@gmail.com',
                'password'      => Hash::make('12345678'),
                'role'          => 'admin',
                'isDeleted'     => 0,
                'haspermission' => 1,
            ]
        );

        // Assign the Spatie 'admin' role (seeded by RolesSeeder)
        $admin->assignRole('admin');

        $this->command->info('Admin user seeded — email: admin@gmail.com');
    }
}
