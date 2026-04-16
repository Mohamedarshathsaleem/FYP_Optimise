<?php
// app/Models/EnergyType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'conversion_coefficient'
    ];
}
