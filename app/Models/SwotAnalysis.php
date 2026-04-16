<?php
// app/Models/SwotAnalysis.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwotAnalysis extends Model
{
    use HasFactory;

    protected $table = 'swot_analyses';

    protected $fillable = [
        'swot_id',
        'title',
        'strengths',
        'weaknesses',
        'opportunities',
        'threats',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->swot_id)) {
                $model->swot_id = self::generateSwotId();
            }
        });
    }

    /**
     * Generate unique SWOT ID
     */
    private static function generateSwotId()
    {
        $lastAnalysis = self::orderBy('id', 'desc')->first();
        $number = $lastAnalysis ? (int) substr($lastAnalysis->swot_id, 2) + 1 : 1;
        return 'SW' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Active' => 'bg-success',
            'Draft' => 'bg-warning',
            'Archived' => 'bg-secondary',
            default => 'bg-info'
        };
    }

    /**
     * Format strengths as array
     */
    public function getStrengthsArrayAttribute()
    {
        return array_filter(explode("\n", $this->strengths));
    }

    /**
     * Format weaknesses as array
     */
    public function getWeaknessesArrayAttribute()
    {
        return array_filter(explode("\n", $this->weaknesses));
    }

    /**
     * Format opportunities as array
     */
    public function getOpportunitiesArrayAttribute()
    {
        return array_filter(explode("\n", $this->opportunities));
    }

    /**
     * Format threats as array
     */
    public function getThreatsArrayAttribute()
    {
        return array_filter(explode("\n", $this->threats));
    }

    /**
     * Scope for active analyses
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for draft analyses
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'Draft');
    }
}
