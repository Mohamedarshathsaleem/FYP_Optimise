<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnergyDataConversionFactor extends Model
{
    protected $table = 'energy_data_conversion_factors';

    protected $fillable = [
        'energy_data_id',
        'from_unit',
        'to_unit',
        'factor',
        'notes',
    ];

    protected $casts = [
        'factor' => 'decimal:8',
    ];

    public function energyData()
    {
        return $this->belongsTo(EnergyData::class, 'energy_data_id');
    }

    /**
     * Resolve the conversion factor for a given energy data source and unit.
     * Falls back to the legacy hardcoded values if no DB record exists.
     */
    public static function resolveForUnit(int $energyDataId, string $unit): float
    {
        $cf = static::where('energy_data_id', $energyDataId)
            ->where('from_unit', $unit)
            ->first();

        if ($cf) {
            return (float) $cf->factor;
        }

        // Fallback to global conversion factors from Conversion Factor Settings
        $energyData = \App\Models\EnergyData::find($energyDataId);
        if ($energyData) {
            $global = \App\Models\ConversionFactor::where('energy_type', $energyData->energy_type)
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
        return ($unit === 'MWh') ? 3.6 : 0.0036;
    }
}
