<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function poeAllocations()
    {
        return $this->hasMany(SecPoeAllocation::class);
    }
}
