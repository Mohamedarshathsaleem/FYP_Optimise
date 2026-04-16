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
        $usersData = [
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@optimise.test',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',         // legacy string column — used by isSuperAdmin()
                'roleName' => 'superadmin',
            ],
            [
                'name'     => 'Top Management',
                'email'    => 'topmanagement@optimise.test',
                'password' => Hash::make('password'),
                'role'     => 'top-management',
                'roleName' => 'top-management',
            ],
            [
                'name'     => 'EMT User',
                'email'    => 'emt@optimise.test',
                'password' => Hash::make('password'),
                'role'     => 'emt',
                'roleName' => 'emt',
            ],
            [
                'name'     => 'Internal REM',
                'email'    => 'internal.rem@optimise.test',
                'password' => Hash::make('password'),
                'role'     => 'internal-rem',
                'roleName' => 'internal-rem',
            ],
            [
                'name'     => 'External REM',
                'email'    => 'external.rem@optimise.test',
                'password' => Hash::make('password'),
                'role'     => 'external-rem',
                'roleName' => 'external-rem',
            ],
        ];

        foreach ($usersData as $data) {
            $roleModel = Role::where('name', $data['roleName'])->first();

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'            => $data['name'],
                    'password'        => $data['password'],
                    'role'            => $data['role'],
                    'default_role_id' => $roleModel?->id,
                ]
            );

            // Sync role into pivot table (role_user) so middleware + permission checks work
            if ($roleModel) {
                $user->roles()->sync([$roleModel->id]);
            }
        }
    }
}
