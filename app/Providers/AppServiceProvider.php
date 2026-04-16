<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // ✅ Simple version - always share menus
        View::composer('layouts.dashboard', function ($view) {
            $user = auth()->user();

            if (!$user) {
                $view->with('menus', collect());
                $view->with('user', null);
                return;
            }

            // Load relationships
            $user->load('roles.permissions');

            // Get menus (optimized query)
            $menus = Menu::with([
                'children' => function ($query) {
                    $query->where('is_active', true)
                          ->orderBy('order')
                          ->with(['children' => function ($q) {
                              $q->where('is_active', true)->orderBy('order');
                          }]);
                }
            ])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

            // Filter based on permissions
            $allowedMenus = $menus->filter(function ($menu) use ($user) {
                return $this->canViewMenu($menu, $user);
            })->values();

            $view->with('menus', $allowedMenus);
            $view->with('user', $user);
        });
    }

    private function canViewMenu($menu, $user)
    {
        // Superadmin bypass
        if ($user->role === 'superadmin') {
            return true;
        }

        // Superadmin only check
        if ($menu->is_superadmin_only) {
            return false;
        }

        // Parent menu with children
        if (!$menu->route && $menu->children->isNotEmpty()) {
            $visibleChildren = $menu->children->filter(function ($child) use ($user) {
                return $this->canViewMenu($child, $user);
            });

            $menu->setRelation('children', $visibleChildren);
            return $visibleChildren->isNotEmpty();
        }

        // Check permission
        return $user->hasPermission($menu->slug . '.view');
    }
}
