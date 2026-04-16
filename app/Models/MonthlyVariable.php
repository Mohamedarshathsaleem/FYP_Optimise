<?php
// app/Models/MonthlyVariable.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'variable_name',
        'category',
    ];

    /**
     * Relationship to MonthlyVariableUsage
     */
    public function usages()
    {
        return $this->hasMany(MonthlyVariableUsage::class);
    }
}