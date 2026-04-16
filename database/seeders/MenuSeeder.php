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
            // 1. Main Dashboard
            [
                'name' => 'Main Dashboard',
                'slug' => 'dashboard',
                'icon' => 'bi bi-grid-1x2-fill',
                'route' => 'dashboard',
                'order' => 1,
                'children' => []
            ],

            // 2. Organizational Context (Parent Menu)
            [
                'name' => 'Organizational Context',
                'slug' => 'organizational-context',
                'icon' => 'bi bi-folder-fill',
                'route' => null, // Parent menu tanpa route
                'order' => 2,
                'children' => [
                    [
                        'name' => 'Internal & External Issues',
                        'slug' => 'internal-external-issues',
                        'route' => 'internal-external-issues',
                        'order' => 1
                    ],
                    [
                        'name' => 'SWOT Analysis',
                        'slug' => 'swot-analysis',
                        'route' => 'swot-analysis',
                        'order' => 2
                    ],
                    [
                        'name' => 'EnMS Scope & Boundaries',
                        'slug' => 'scope-boundaries',
                        'route' => 'scope-boundaries',
                        'order' => 3
                    ],
                    [
                        'name' => 'Legal',
                        'slug' => 'legals',
                        'route' => 'legals',
                        'order' => 4
                    ],
                    [
                        'name' => 'Stakeholders',
                        'slug' => 'stakeholders',
                        'route' => 'stakeholders',
                        'order' => 5
                    ],
                ]
            ],

            // 3. Energy Management Committee
            [
                'name' => 'Energy Management Committee',
                'slug' => 'committees',
                'icon' => 'bi bi-lightning-charge-fill',
                'route' => 'committees',
                'order' => 3,
                'children' => []
            ],

            // 4. Energy Policy
            [
                'name' => 'Energy Policy',
                'slug' => 'energy-policy',
                'icon' => 'bi bi-lightning-charge-fill',
                'route' => 'energy-policy',
                'order' => 4,
                'children' => []
            ],

            // 5. Energy Data Management
            [
                'name' => 'Energy Data Management',
                'slug' => 'energy-data-management',
                'icon' => 'bi bi-lightning-charge-fill',
                'route' => 'admin/energy-data-management',
                'order' => 5,
                'children' => []
            ],

            // 6. Energy Review (Parent)
            [
                'name' => 'Energy Review',
                'slug' => 'energy-review',
                'icon' => 'bi bi-graph-up',
                'route' => null,
                'order' => 6,
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
                        'name' => 'Utility Apportioning',
                        'slug' => 'utility-apportioning',
                        'route' => 'utility-apportioning',
                        'order' => 4
                    ],
                    [
                        'name' => 'SEU Flagging',
                        'slug' => 'seu-flagging',
                        'route' => 'seu-flagging',
                        'order' => 5
                    ],
                ]
            ],

            // 7. Action Plan (Parent Menu with nested children)
            [
                'name' => 'Action Plan',
                'slug' => 'action-plan',
                'icon' => 'bi bi-list-task',
                'route' => null,
                'order' => 7,
                'children' => [
                    [
                        'name' => 'Overview',
                        'slug' => 'action-plan-overview',
                        'route' => 'action-plan/overview',
                        'order' => 1
                    ],
                    [
                        'name' => 'Yearly Action Plan/Support',
                        'slug' => 'action-plan-yearly',
                        'route' => 'action-plan/yearly',
                        'order' => 2
                    ],
                    [
                        'name' => 'Action Plan Development',
                        'slug' => 'action-plan-development',
                        'route' => null, // Parent submenu
                        'order' => 3,
                        'children' => [
                            [
                                'name' => 'Motivation Strategy',
                                'slug' => 'motivation-strategy',
                                'route' => 'action-plan/motivation-strategy',
                                'order' => 1
                            ],
                            [
                                'name' => 'Communication & Awareness',
                                'slug' => 'communication-awareness',
                                'route' => 'action-plan/communication-awareness',
                                'order' => 2
                            ],
                            [
                                'name' => 'Training Plan',
                                'slug' => 'training-plan',
                                'route' => 'action-plan/training-plan',
                                'order' => 3
                            ],
                        ]
                    ]
                ]
            ],

            // 8. EnPI & Baseline Management
            [
                'name' => 'EnPI & Baseline Management',
                'slug' => 'enpi-baseline-management',
                'icon' => 'bi bi-bar-chart',
                'route' => 'enpi-baseline-management',
                'order' => 8,
                'children' => []
            ],

            // 9. Settings
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'icon' => 'bi bi-gear-fill',
                'route' => 'settings',
                'order' => 9,
                'children' => []
            ],

            // 10. User Management (Superadmin only)
            [
                'name' => 'User Management',
                'slug' => 'user-management',
                'icon' => 'bi bi-people-fill',
                'route' => null,
                'order' => 10,
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
