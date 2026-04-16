<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EipNormalizationFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'factor_type',
        'factor_value',
        'notes',
    ];

    protected $casts = [
        'factor_value' => 'decimal:4',
    ];
}
