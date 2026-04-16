<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
{
    // Ambil semua role dari database (nama role saja)
    $roles = Role::pluck('name');
    return view('admin.users.create', compact('roles'));
}

   public function store(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role'     => 'required|in:superadmin,top-management,rem,user',
    ]);

    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => $request->role,
    ]);

    // ✅ Tambahkan ini: sync role ke pivot table
    $role = Role::where('name', $request->role)->first();
    if ($role) {
        $user->roles()->sync([$role->id]);
        $user->update(['default_role_id' => $role->id]);
    }

    return redirect()->route('admin.users.index')
                     ->with('success', 'User created successfully!');
}
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
{
    $roles = Role::pluck('name');
    return view('admin.users.edit', compact('user', 'roles'));
}

  public function update(Request $request, User $user)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'password' => 'nullable|string|min:8|confirmed',
        'role'     => 'required|in:superadmin,top-management,rem,user',
    ]);

    $updateData = [
        'name'  => $request->name,
        'email' => $request->email,
        'role'  => $request->role,
    ];

    if ($request->filled('password')) {
        $updateData['password'] = Hash::make($request->password);
    }

    $user->update($updateData);

    // Sync role ke pivot table
    $role = Role::where('name', $request->role)->first();
    if ($role) {
        $user->roles()->sync([$role->id]);
        $user->update(['default_role_id' => $role->id]);
    }

    return redirect()->route('admin.users.index')
                     ->with('success', 'User updated successfully!');
}

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'You cannot delete yourself!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User deleted successfully!');
    }

    private function getDefaultPermissions($role)
    {
        $permissions = [
            'dashboard' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
            'issues' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
            'boundaries' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
            'legal' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
            'stakeholders' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
            'committee' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
            'motivation' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
            'communication' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
            'training' => ['can_view' => false, 'can_add' => false, 'can_edit' => false, 'can_delete' => false],
        ];

        switch ($role) {
            case 'superadmin':
                foreach ($permissions as $module => &$actions) {
                    foreach ($actions as $action => &$value) {
                        $value = true;
                    }
                }
                break;
            case 'management':
                foreach ($permissions as $module => &$actions) {
                    $actions['can_view'] = true;
                }
                break;
            case 'rem':
                foreach ($permissions as $module => &$actions) {
                    $actions['can_view'] = true;
                    $actions['can_add'] = true;
                    $actions['can_edit'] = true;
                }
                $permissions['boundaries']['can_view'] = false;
                break;
            case 'user':
                $permissions['dashboard']['can_view'] = true;
                break;
        }

        return $permissions;
    }
}
