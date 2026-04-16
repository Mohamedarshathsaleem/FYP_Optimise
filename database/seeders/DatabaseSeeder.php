<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MenuSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RolesAndPermissionsSeeder::class,

            UsersTableSeeder::class,
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
