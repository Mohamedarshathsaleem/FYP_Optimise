<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecPoeAllocation extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'year', 'percentage', 'poe_category'];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
