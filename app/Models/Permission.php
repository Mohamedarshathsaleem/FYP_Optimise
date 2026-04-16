<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',        
        'description',
        'menu_id'
    ];

    /**
     * Relationship with roles (Many-to-Many)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id');
    }

    /**
     * Relationship with menu
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
