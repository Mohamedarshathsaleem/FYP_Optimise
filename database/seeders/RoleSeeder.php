<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create the 5 actor roles — permission assignment happens in RolePermissionSeeder
        // which runs after PermissionSeeder so permissions actually exist.

        Role::updateOrCreate(
            ['name' => 'superadmin'],
            ['description' => 'Full system access — manages users, permissions, all modules, and system configuration.']
        );

        Role::updateOrCreate(
            ['name' => 'top-management'],
            ['description' => 'Reviews high-level energy performance data, approves baseline models and energy policy.']
        );

        Role::updateOrCreate(
            ['name' => 'emt'],
            ['description' => 'Energy Management Team — enters and manages energy data, production data, and monthly variables.']
        );

        Role::updateOrCreate(
            ['name' => 'internal-rem'],
            ['description' => 'Internal REM — technical evaluations, SEC/EIP analysis, SEU identification, baseline model development.']
        );

        Role::updateOrCreate(
            ['name' => 'external-rem'],
            ['description' => 'External REM — independent validation, audit energy performance, verify SEC/EIP and SEU results.']
        );
    }
}
