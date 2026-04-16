<?php
// app/Models/MonthlyProductionUsage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyProductionUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_production_id',
        'month',
        'production_amount',
        'production_unit',
        'notes'
    ];

    /**
     * Relationship to MonthlyProduction
     */
    public function monthlyProduction()
    {
        return $this->belongsTo(MonthlyProduction::class);
    }
}