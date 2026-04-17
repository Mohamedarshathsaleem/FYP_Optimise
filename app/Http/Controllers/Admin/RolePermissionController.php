<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $users = User::with('roles.permissions')->paginate(20);
        return view('admin.user-permissions.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.user-permissions.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles'   => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->roles()->sync($request->roles ?? []);

        return redirect()->route('admin.user-permissions.index')
            ->with('success', "Roles updated successfully for {$user->name}.");
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'user_ids'  => ['required', 'array'],
            'user_ids.*'=> ['integer', 'exists:users,id'],
            'bulk_role' => ['nullable', 'string', 'exists:roles,name'],
        ]);

        if (!$request->filled('bulk_role')) {
            return redirect()->back()->with('success', 'No role change applied.');
        }

        $role = Role::where('name', $request->bulk_role)->firstOrFail();

        User::whereIn('id', $request->user_ids)->each(function (User $user) use ($role) {
            $user->roles()->sync([$role->id]);
        });

        return redirect()->route('admin.user-permissions.index')
            ->with('success', 'Bulk role update applied successfully.');
    }
}
