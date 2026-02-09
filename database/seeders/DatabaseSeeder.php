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
            DepartmentSeeder::class,
            CategorySeeder::class,
            TicketStatusSeeder::class,
            RolePermissionSeeder::class,
            InitialMasterAdminSeeder::class,
            
            // Dummy data seeders (optional - comment jika tidak perlu)
            // DummyUserSeeder::class,
            // DummyTicketSeeder::class,
        ]);
    }
}
