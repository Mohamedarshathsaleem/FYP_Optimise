<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Get roles (matching RoleSeeder: superadmin, top-management, rem, user)
        $superadminRole = Role::where('name', 'superadmin')->first();
        $remRole = Role::where('name', 'rem')->first();
        $userRole = Role::where('name', 'user')->first();

        // 1. Superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
                'default_role_id' => $superadminRole?->id,
            ]
        );

        // 2. REM User (previously admin - using valid role from RoleSeeder)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'rem',
                'default_role_id' => $remRole?->id,
            ]
        );

        // 3. Normal User
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'default_role_id' => $userRole?->id,
            ]
        );
    }
}
