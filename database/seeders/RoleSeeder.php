<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $superadmin = Role::updateOrCreate(['name' => 'superadmin'], ['description' => 'Super Admin Role']);
        $management = Role::updateOrCreate(['name' => 'top-management'], ['description' => 'Top Management Role']);
        $rem = Role::updateOrCreate(['name' => 'rem'], ['description' => 'Internal REM Role']);
        $user = Role::updateOrCreate(['name' => 'user'], ['description' => 'Regular User']);

        // Superadmin gets all permissions
        $allPermissionIds = Permission::pluck('id');
        $superadmin->permissions()->sync($allPermissionIds);

        // Management gets view-only permissions for all menus + export permissions
        $managementPerms = Permission::where('name', 'like', '%.view')
            ->orWhere('name', 'like', '%.export')
            ->orWhere('name', 'like', '%.approval')
            ->pluck('id');
        $management->permissions()->sync($managementPerms);

        // REM gets view, add, edit permissions (no delete)
        $remPerms = Permission::where('name', 'like', '%.view')
            ->orWhere('name', 'like', '%.add')
            ->orWhere('name', 'like', '%.edit')
            ->pluck('id');
        $rem->permissions()->sync($remPerms);

        // Regular user gets limited permissions
        $userPermissionNames = [
            'dashboard.view',
            'energy-policy.view',
            'enpi-baseline-management.view',
        ];
        $userPermIds = Permission::whereIn('name', $userPermissionNames)->pluck('id');
        $user->permissions()->sync($userPermIds);
    }
}
