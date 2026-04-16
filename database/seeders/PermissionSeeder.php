<?php

namespace Database\Seeders;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Standard actions for all menus
        $actions = ['view', 'add', 'edit', 'delete', 'import','approval','export'];
        $menus = Menu::all();

        // Create permissions for each menu with standard actions
        foreach ($menus as $menu) {
            foreach ($actions as $action) {
                $permissionName = "{$menu->slug}.{$action}";
                Permission::updateOrCreate(
                    ['name' => $permissionName],
                    [
                        'slug' => $permissionName,
                        'description' => "{$menu->name} - {$action} access",
                        'menu_id' => $menu->id
                    ]
                );
            }
        }

        // Additional specific permissions for SWOT Analysis
        $swotMenu = Menu::where('slug', 'swot-analysis')->first();
        if ($swotMenu) {
            $swotActions = ['export', 'approval'];
            foreach ($swotActions as $action) {
                $permissionName = "swot-analysis.{$action}";
                Permission::updateOrCreate(
                    ['name' => $permissionName],
                    [
                        'slug' => $permissionName,
                        'description' => "SWOT Analysis - {$action} access",
                        'menu_id' => $swotMenu->id
                    ]
                );
            }
        }

        // Additional specific permissions for Risk & Opportunity (Internal-External Issues)
        $riskMenu = Menu::where('slug', 'internal-external-issues')->first();
        if ($riskMenu) {
            $riskActions = ['approval'];
            foreach ($riskActions as $action) {
                $permissionName = "internal-external-issues.{$action}";
                Permission::updateOrCreate(
                    ['name' => $permissionName],
                    [
                        'slug' => $permissionName,
                        'description' => "Internal & External Issues - {$action} access",
                        'menu_id' => $riskMenu->id
                    ]
                );
            }
        }
    }
}
