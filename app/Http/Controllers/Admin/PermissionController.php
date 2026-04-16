<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::paginate(15);
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'module_name' => ['required', 'regex:/^[a-z0-9._-]+$/i', 'max:50'],
            'actions' => ['required', 'array'],
            'actions.*' => ['in:view,add,edit,delete,import'],
            'description' => ['nullable','string'],
        ]);

        $module = strtolower($request->module_name);
        $actions = $request->actions;
        $desc = $request->description;

        // Loop buat permission satu-per-satu
        foreach ($actions as $action) {
            $permName = "{$module}.{$action}";
            // Cek kalau permission sudah ada, skip
            if (!Permission::where('name', $permName)->exists()) {
                Permission::create([
                    'name' => $permName,
                    'description' => $desc,
                ]);
            }
        }

        return redirect()->route('admin.permissions.index')->with('success', 'Permission module and actions added successfully!');
    }


    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
        ]);
        $permission->update($request->only('name', 'description'));

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
