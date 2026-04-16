<?php
// app/Http/Controllers/Admin/UserPermissionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.user-permissions.index', compact('users'));
    }

    public function edit(User $user)
    {
        $modules = [
            'dashboard' => 'Dashboard',
            'issues' => 'Internal & External Issues',
            'boundaries' => 'EnMS Scope & Boundaries',
            'legal' => 'Legal Requirements',
            'stakeholders' => 'Stakeholders',
            'committee' => 'Energy Committee',
            'motivation' => 'Motivation Strategy',
            'communication' => 'Communication & Awareness',
            'training' => 'Training Plan'
        ];

        $actions = [
            'can_view' => 'View',
            'can_add' => 'Add',
            'can_edit' => 'Edit',
            'can_delete' => 'Delete'
        ];

        return view('admin.user-permissions.edit', compact('user', 'modules', 'actions'));
    }

    public function update(Request $request, User $user)
    {
        $permissions = [];

        // Process permissions from form
        $formPermissions = $request->input('permissions', []);

        foreach ($formPermissions as $module => $modulePermissions) {
            $permissions[$module] = [
                'can_view' => isset($modulePermissions['can_view']),
                'can_add' => isset($modulePermissions['can_add']),
                'can_edit' => isset($modulePermissions['can_edit']),
                'can_delete' => isset($modulePermissions['can_delete'])
            ];
        }

        $user->update([
            'permissions' => $permissions,
            'role' => $request->input('role', $user->role)
        ]);

        return redirect()->route('admin.user-permissions.index')
                        ->with('success', "Permissions updated successfully for {$user->name}!");
    }

    public function bulkUpdate(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        $role = $request->input('bulk_role');
        $permissions = $request->input('bulk_permissions', []);

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $updateData = [];

                if ($role) {
                    $updateData['role'] = $role;
                }

                if (!empty($permissions)) {
                    $updateData['permissions'] = $permissions;
                }

                $user->update($updateData);
            }
        }

        return redirect()->back()->with('success', 'Bulk permissions updated successfully!');
    }
}
