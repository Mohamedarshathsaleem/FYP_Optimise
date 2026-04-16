<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EipWeatherData extends Model
{
    use HasFactory;

    protected $table = 'eip_weather_data';

    protected $fillable = [
        'month',
        'avg_temperature',
        'avg_humidity',
        'heating_degree_days',
        'cooling_degree_days',
    ];

    protected $casts = [
        'avg_temperature' => 'decimal:2',
        'avg_humidity' => 'decimal:2',
        'heating_degree_days' => 'decimal:2',
        'cooling_degree_days' => 'decimal:2',
    ];
}
