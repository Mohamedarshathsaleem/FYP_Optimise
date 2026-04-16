<?php
// app/Models/EnergyData.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyData extends Model
{
    use HasFactory;

    protected $fillable = [
        'energy_type',
        'provider',
        'account_no',
        'contract_type',
        'category',
    ];

    /**
     * Relationship to EnergyDataUsage
     */
    public function usages()
    {
        return $this->hasMany(EnergyDataUsage::class);
    }

    /**
     * Relationship to EnergyDataConversionFactor
     */
    public function conversionFactors()
    {
        return $this->hasMany(EnergyDataConversionFactor::class, 'energy_data_id');
    }
}