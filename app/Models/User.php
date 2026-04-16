<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'default_role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function defaultRole()
    {
        return $this->belongsTo(Role::class, 'default_role_id');
    }

    /**
     * ✅ Check permission using 'name' column instead of 'slug'
     */
    public function hasPermission($permissionName)
    {
        // Superadmin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Load roles if not loaded
        if (!$this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        // Check if user has roles
        if ($this->roles->isEmpty()) {
            return false;
        }

        // Get all permissions from all roles
        $allPermissions = $this->roles->flatMap(function ($role) {
            return $role->permissions;
        });

        // Check if permission name exists (using 'name' column)
        return $allPermissions->contains('name', $permissionName);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function getAllPermissions()
    {
        

        if (!$this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        return $this->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id');
    }
}
