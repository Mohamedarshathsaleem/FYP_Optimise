<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionFactor extends Model
{
    protected $fillable = [
        'energy_type',
        'from_unit',
        'to_unit',
        'factor',
        'is_default',
        'organization_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'factor' => 'decimal:8',
        'is_default' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the effective conversion factor for an energy type/unit pair.
     * Organization-specific overrides take priority over defaults.
     */
    public static function getEffective($energyType, $fromUnit, $toUnit, $organizationId = null)
    {
        // Try organization-specific first
        if ($organizationId) {
            $orgFactor = static::where('energy_type', $energyType)
                ->where('from_unit', $fromUnit)
                ->where('to_unit', $toUnit)
                ->where('organization_id', $organizationId)
                ->first();

            if ($orgFactor) {
                return $orgFactor;
            }
        }

        // Fall back to default
        return static::where('energy_type', $energyType)
            ->where('from_unit', $fromUnit)
            ->where('to_unit', $toUnit)
            ->where('is_default', true)
            ->first();
    }
}
