<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,              // creates the 5 actor roles
            MenuSeeder::class,              // menus — PermissionSeeder reads Menu::all()
            PermissionSeeder::class,        // creates permissions from menus
            RolePermissionSeeder::class,    // assigns permissions to roles (needs both above)
            UsersTableSeeder::class,        // creates users + syncs role_user pivot
            UserPermissionSeeder::class,

            LegalItemSeeder::class,
            StakeholderSeeder::class,

            EnergyTypeSeeder::class,
            LoadApportioningApproachSeeder::class,
            

            // Energy Data Management usage seeders (must run before SEC seeder)
            EnergyDataUsageSeeder::class,
            EnergyResourceUsageSeeder::class,
            MonthlyProductionUsageSeeder::class,
            MonthlyVariableUsageSeeder::class,

            // SEC-specific seeder (POE allocations only - depends on products from above)
            SecMockDataSeeder::class,
        ]);
    }
}
