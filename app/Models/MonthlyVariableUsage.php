<?php
// app/Models/MonthlyVariableUsage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyVariableUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_variable_id',
        'month',
        'variable_value',
        'variable_unit',
        'notes'
    ];

    /**
     * Relationship to MonthlyVariable
     */
    public function monthlyVariable()
    {
        return $this->belongsTo(MonthlyVariable::class);
    }
}