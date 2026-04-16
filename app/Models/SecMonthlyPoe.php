<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecMonthlyPoe extends Model
{
    protected $table = 'sec_monthly_poe';

    protected $fillable = [
        'product_id',
        'month',
        'poe_category',
        'percentage',
        'created_by',
    ];

    protected $casts = [
        'percentage' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
