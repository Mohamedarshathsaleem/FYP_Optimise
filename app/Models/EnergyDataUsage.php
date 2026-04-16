<?php
// app/Models/EnergyDataUsage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyDataUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'energy_data_id',
        'month',
        'usage_value',
        'usage_unit',
        'usage_gj',
        'cost',
        'notes'
    ];

    /**
     * Relationship to EnergyData
     */
    public function energyData()
    {
        return $this->belongsTo(EnergyData::class);
    }

    /**
     * Calculate GJ from usage value and unit
     */
    public static function calculateGJ($value, $unit)
    {
        $multiplier = ($unit === 'MWh') ? 3.6 : 0.0036;
        return round($value * $multiplier, 3);
    }
}