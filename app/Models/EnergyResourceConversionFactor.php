<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnergyResourceConversionFactor extends Model
{
    protected $table = 'energy_resource_conversion_factors';

    protected $fillable = [
        'energy_resource_data_id',
        'from_unit',
        'to_unit',
        'factor',
        'notes',
    ];

    protected $casts = [
        'factor' => 'decimal:8',
    ];

    public function energyResourceData()
    {
        return $this->belongsTo(EnergyResourceData::class, 'energy_resource_data_id');
    }

    /**
     * Resolve the conversion factor for a given energy resource source and unit.
     * Falls back to the legacy hardcoded values if no DB record exists.
     */
    public static function resolveForUnit(int $resourceDataId, string $unit): float
    {
        $cf = static::where('energy_resource_data_id', $resourceDataId)
            ->where('from_unit', $unit)
            ->first();

        if ($cf) {
            return (float) $cf->factor;
        }

        // Fallback to global conversion factors from Conversion Factor Settings
        $resourceData = EnergyResourceData::find($resourceDataId);
        if ($resourceData) {
            $global = ConversionFactor::where('energy_type', $resourceData->resource_type)
                ->where('from_unit', $unit)
                ->where(function ($q) {
                    $q->where('is_default', true)
                      ->orWhere('organization_id', auth()->id());
                })
                ->orderBy('is_default')
                ->first();

            if ($global) {
                return (float) $global->factor;
            }
        }

        // Last resort hardcoded fallback
        $defaults = [
            'kWh'    => 0.0036,
            'MWh'    => 3.6,
            'L'      => 0.0347,
            'kg'     => 0.0464,
            'ton'    => 46.4,
            'Gallon' => 0.131,
            'm3'     => 0.0378,
        ];

        return $defaults[$unit] ?? 1.0;
    }
}
