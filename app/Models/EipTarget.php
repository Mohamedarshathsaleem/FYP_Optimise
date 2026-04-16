<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EipTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'target_type',
        'target_value',
        'seu_threshold',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'target_value' => 'decimal:4',
        'seu_threshold' => 'decimal:4',
    ];
}
