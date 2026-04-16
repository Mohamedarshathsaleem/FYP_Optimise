<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnergyUnit extends Model
{
    protected $table = 'energy_units';

    protected $fillable = [
        'code',
        'name',
        'unit_type',
        'symbol',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('unit_type', $type);
    }

    public static function activeUnits()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public static function activeSymbols()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }
}
