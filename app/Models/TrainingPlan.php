<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'competency_area',
        'required_knowledge', 
        'target_group',
        'competency_level',
        'training_needs',
        'training_method',
        'frequency'
    ];

    // Cast competency_level sebagai string biasa
    protected $casts = [
        'required_knowledge' => 'array',
        'training_needs' => 'array'
    ];
}