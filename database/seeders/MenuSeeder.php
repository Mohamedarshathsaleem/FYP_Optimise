<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        Menu::query()->delete();

        $menus = [
            // 1. Energy Data Management
            [
                'name' => 'Energy Data Management',
                'slug' => 'energy-data-management',
                'icon' => 'bi bi-lightning-charge-fill',
                'route' => 'admin/energy-data-management',
                'order' => 1,
                'children' => []
            ],

            // 2. Energy Review (Parent)
            [
                'name' => 'Energy Review',
                'slug' => 'energy-review',
                'icon' => 'bi bi-graph-up',
                'route' => null,
                'order' => 2,
                'children' => [
                    [
                        'name' => 'SEC Analysis',
                        'slug' => 'sec-analysis',
                        'route' => 'sec-analysis',
                        'order' => 1
                    ],
                    [
                        'name' => 'EIP Analysis',
                        'slug' => 'eip-analysis',
                        'route' => 'eip-analysis',
                        'order' => 2
                    ],
                    [
                        'name' => 'Load Apportioning',
                        'slug' => 'load-apportioning',
                        'route' => 'load-apportioning',
                        'order' => 3
                    ],
                    [
                        'name' => 'SEU Flagging',
                        'slug' => 'seu-flagging',
                        'route' => 'seu-flagging',
                        'order' => 4
                    ],
                ]
            ],

            // 3. EnPI & Baseline Management
            [
                'name' => 'EnPI & Baseline Management',
                'slug' => 'enpi-baseline-management',
                'icon' => 'bi bi-bar-chart',
                'route' => 'enpi-baseline-management',
                'order' => 3,
                'children' => []
            ],

            // 4. User Management (Superadmin only)
            [
                'name' => 'User Management',
                'slug' => 'user-management',
                'icon' => 'bi bi-people-fill',
                'route' => null,
                'order' => 4,
                'is_superadmin_only' => true,
                'children' => [
                    [
                        'name' => 'Users',
                        'slug' => 'users',
                        'route' => 'admin/users',
                        'order' => 1
                    ],
                    [
                        'name' => 'Permissions',
                        'slug' => 'permissions',
                        'route' => 'admin/permissions',
                        'order' => 2
                    ],
                    [
                        'name' => 'Roles',
                        'slug' => 'roles',
                        'route' => 'admin/roles',
                        'order' => 3
                    ],
                ]
            ]
        ];

        foreach ($menus as $menu) {
            $this->createMenu($menu);
        }
    }

    private function createMenu($data, $parentId = null, $level = 0)
    {
        $menu = Menu::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'icon' => $data['icon'] ?? null,
            'route' => $data['route'] ?? null,
            'parent_id' => $parentId,
            'order' => $data['order'] ?? 0,
            'is_active' => true,
            'level' => $level,
            'is_superadmin_only' => $data['is_superadmin_only'] ?? false
        ]);

        if (!empty($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->createMenu($child, $menu->id, $level + 1);
            }
        }

        return $menu;
    }
}
