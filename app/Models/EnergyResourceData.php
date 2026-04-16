<?php
// app/Models/EnergyResourceData.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyResourceData extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_type',
        'provider',
        'account_no',
        'contract_type',
        'category',
    ];

    /**
     * Relationship to EnergyResourceUsage
     */
    public function usages()
    {
        return $this->hasMany(EnergyResourceUsage::class);
    }

    /**
     * Relationship to EnergyResourceConversionFactor
     */
    public function conversionFactors()
    {
        return $this->hasMany(EnergyResourceConversionFactor::class, 'energy_resource_data_id');
    }
}