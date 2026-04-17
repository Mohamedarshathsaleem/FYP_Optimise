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

        // ── Top Management: view + export on all operational modules;
        //    approval only on EnPI & Baseline (strategic sign-off) ────────────
        if ($topMgmt) {
            $ids = Permission::where(function ($q) {
                $q->where('name', 'like', '%.view')
                  ->orWhere('name', 'like', '%.export')
                  ->orWhere('name', 'enpi-baseline-management.approval');
            })->whereNotIn('name', $adminOnlyPerms)->pluck('id');
            $topMgmt->permissions()->sync($ids);
        }

        // ── EMT: full CRUD on Energy Data Management only ────────────────────
        if ($emt) {
            $ids = Permission::whereIn('name', [
                'energy-data-management.view',
                'energy-data-management.add',
                'energy-data-management.edit',
                'energy-data-management.delete',
                'energy-data-management.import',
            ])->pluck('id');
            $emt->permissions()->sync($ids);
        }

        // ── Internal REM: full access on Energy Review + EnPI/Baseline;
        //    view-only on Energy Data Management ────────────────────────────
        if ($internalRem) {
            $reviewSlugs = [
                'energy-review', 'sec-analysis', 'eip-analysis',
                'load-apportioning', 'utility-apportioning', 'seu-flagging',
                'enpi-baseline-management',
            ];
            $reviewIds = Permission::where(function ($q) use ($reviewSlugs) {
                foreach ($reviewSlugs as $slug) {
                    $q->orWhere('name', 'like', $slug . '.%');
                }
            })->pluck('id');
            $dataViewId = Permission::where('name', 'energy-data-management.view')->pluck('id');
            $internalRem->permissions()->sync($reviewIds->merge($dataViewId));
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
