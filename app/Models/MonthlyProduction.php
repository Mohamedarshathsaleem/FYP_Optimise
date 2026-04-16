<?php
// app/Models/MonthlyProduction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_type',
        'category',
    ];

    /**
     * Relationship to MonthlyProductionUsage
     */
    public function usages()
    {
        return $this->hasMany(MonthlyProductionUsage::class);
    }
}