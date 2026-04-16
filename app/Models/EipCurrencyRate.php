<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EipCurrencyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_code',
        'rate_to_myr',
        'effective_date',
    ];

    protected $casts = [
        'rate_to_myr' => 'decimal:6',
        'effective_date' => 'date',
    ];
}
