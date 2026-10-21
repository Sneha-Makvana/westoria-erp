<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ModulesSeeder::class,    // Fixed module ids — must run before user permission seeding
            RolesSeeder::class,      // Must run first — AdminUserSeeder depends on roles existing
            AdminUserSeeder::class,
        ]);
    }
}
