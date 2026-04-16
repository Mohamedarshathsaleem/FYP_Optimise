<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Assign permissions to each of the 5 actor roles.
     * Must run AFTER PermissionSeeder (permissions must exist).
     */
    public function run()
    {
        $superadmin   = Role::where('name', 'superadmin')->first();
        $topMgmt      = Role::where('name', 'top-management')->first();
        $emt          = Role::where('name', 'emt')->first();
        $internalRem  = Role::where('name', 'internal-rem')->first();
        $externalRem  = Role::where('name', 'external-rem')->first();

        // Permissions excluded from all non-superadmin roles
        $adminOnlyPerms = ['users.add', 'users.edit', 'users.delete', 'users.view',
                           'roles.add', 'roles.edit', 'roles.delete', 'roles.view',
                           'permissions.add', 'permissions.edit', 'permissions.delete', 'permissions.view'];

        // ── Superadmin: all permissions ──────────────────────────────────────
        if ($superadmin) {
            $superadmin->permissions()->sync(Permission::pluck('id'));
        }

        // ── Top Management: view + export + approval ─────────────────────────
        if ($topMgmt) {
            $ids = Permission::where(function ($q) {
                $q->where('name', 'like', '%.view')
                  ->orWhere('name', 'like', '%.export')
                  ->orWhere('name', 'like', '%.approval');
            })->whereNotIn('name', $adminOnlyPerms)->pluck('id');
            $topMgmt->permissions()->sync($ids);
        }

        // ── EMT: view + add + edit + import (no delete, no admin) ────────────
        if ($emt) {
            $ids = Permission::where(function ($q) {
                $q->where('name', 'like', '%.view')
                  ->orWhere('name', 'like', '%.add')
                  ->orWhere('name', 'like', '%.edit')
                  ->orWhere('name', 'like', '%.import');
            })->whereNotIn('name', $adminOnlyPerms)->pluck('id');
            $emt->permissions()->sync($ids);
        }

        // ── Internal REM: view + add + edit + delete + export + approval ─────
        if ($internalRem) {
            $ids = Permission::where(function ($q) {
                $q->where('name', 'like', '%.view')
                  ->orWhere('name', 'like', '%.add')
                  ->orWhere('name', 'like', '%.edit')
                  ->orWhere('name', 'like', '%.delete')
                  ->orWhere('name', 'like', '%.export')
                  ->orWhere('name', 'like', '%.import')
                  ->orWhere('name', 'like', '%.approval');
            })->whereNotIn('name', $adminOnlyPerms)->pluck('id');
            $internalRem->permissions()->sync($ids);
        }

        // ── External REM: view + export only (read-only audit role) ──────────
        if ($externalRem) {
            $ids = Permission::where(function ($q) {
                $q->where('name', 'like', '%.view')
                  ->orWhere('name', 'like', '%.export');
            })->whereNotIn('name', $adminOnlyPerms)->pluck('id');
            $externalRem->permissions()->sync($ids);
        }
    }
}
