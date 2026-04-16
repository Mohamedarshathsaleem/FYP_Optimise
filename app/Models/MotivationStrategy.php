<?php

// app/Models/MotivationStrategy.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotivationStrategy extends Model
{
    protected $table = 'motivation_strategies'; // jika Laravel salah guess

    protected $fillable = [
        'motivation_activity',
        'target_group',
        'criteria_for_recognition',
        'recognition_method',
        'frequency',
        'responsible_dept',
        'remarks',
    ];
}