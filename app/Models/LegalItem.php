<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'legal_id',
        'item_id',
        'description',
        'details',
        'is_active'
    ];

    protected $casts = [
        'details' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Relationship with Legal
     */
    public function legal()
    {
        return $this->belongsTo(Legal::class);
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
