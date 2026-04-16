<?php
// app/Models/EnergyResourceUsage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyResourceUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'energy_resource_data_id',
        'month',
        'usage_value',
        'usage_unit',
        'usage_gj',
        'cost',
        'notes'
    ];

    /**
     * Relationship to EnergyResourceData
     */
    public function energyResourceData()
    {
        return $this->belongsTo(EnergyResourceData::class);
    }

    /**
     * Calculate GJ from usage value and unit
     * This is a simplified conversion - adjust based on your actual resource types
     */
    public static function calculateGJ($value, $unit)
    {
        // Conversion factors (these are examples - adjust to your needs)
        $conversions = [
            'kWh' => 0.0036,
            'MWh' => 3.6,
            'L' => 0.0347,      // Diesel/fuel oil approximation
            'kg' => 0.0464,     // Natural gas approximation
            'ton' => 46.4,      // Natural gas approximation
            'Gallon' => 0.131,  // Fuel approximation
            'm3' => 0.0378,     // Natural gas approximation
        ];
        
        $multiplier = $conversions[$unit] ?? 1;
        return round($value * $multiplier, 3);
    }
}