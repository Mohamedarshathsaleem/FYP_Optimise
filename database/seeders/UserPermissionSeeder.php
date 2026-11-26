<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\Support\SeedPassword;

class UserPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Same default as UsersTableSeeder; one password for every demo account
        $password = SeedPassword::resolve('password');

        if ($password === null) {
            $this->command?->warn('SEED_USER_PASSWORD not set in production; skipping demo users.');

            return;
        }

        $hashedPassword = Hash::make($password);

        // Get roles
        $superadminRole = Role::where('name', 'superadmin')->first();
        $managementRole = Role::where('name', 'top-management')->first();
        $remRole = Role::where('name', 'rem')->first();
        $userRole = Role::where('name', 'user')->first();

        // 1. Super Admin User
        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => $hashedPassword,
                'role' => 'superadmin',
                'default_role_id' => $superadminRole?->id,
            ]
        );
        if ($superadminRole) {
            $superAdminUser->roles()->sync([$superadminRole->id]);
        }

        // 2. Top Management User
        $managementUser = User::updateOrCreate(
            ['email' => 'topmanagement@example.com'],
            [
                'name' => 'Top Management',
                'password' => $hashedPassword,
                'role' => 'top-management',
                'default_role_id' => $managementRole?->id,
            ]
        );
        if ($managementRole) {
            $managementUser->roles()->sync([$managementRole->id]);
        }

        // 3. Internal REM User
        $remUser = User::updateOrCreate(
            ['email' => 'rem@demo.com'],
            [
                'name' => 'Internal REM',
                'password' => $hashedPassword,
                'role' => 'rem',
                'default_role_id' => $remRole?->id,
            ]
        );
        if ($remRole) {
            $remUser->roles()->sync([$remRole->id]);
        }

        // 4. Regular User
        $regularUser = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Normal User',
                'password' => $hashedPassword,
                'role' => 'user',
                'default_role_id' => $userRole?->id,
            ]
        );
        if ($userRole) {
            $regularUser->roles()->sync([$userRole->id]);
        }
    }
}
