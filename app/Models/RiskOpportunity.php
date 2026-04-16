<?php
// app/Models/RiskOpportunity.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RiskOpportunity extends Model
{
    use HasFactory;

    protected $table = 'risks_opportunities';

    protected $fillable = [
        'risk_id',
        'issue',
        'type',
        'category',
        'likelihood',
        'risk_level',
        'impact_description',
        'mitigation_actions',
        'responsible_person',
        'review_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'mitigation_actions' => 'array',
        'review_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->risk_id)) {
                $model->risk_id = self::generateRiskId();
            }
        });
    }

    /**
     * Generate unique risk ID - FIXED VERSION
     */
    private static function generateRiskId()
    {
        // Gunakan DB query langsung untuk menghindari infinite loop
        $lastRisk = \DB::table('risks_opportunities')
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastRisk ? (int) substr($lastRisk->risk_id, 2) + 1 : 1;
        return 'RO' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get risk level badge class - FIXED VERSION
     */
    public function getRiskLevelBadgeAttribute()
    {
        return match($this->risk_level) {
            'Low' => 'bg-info',
            'Medium' => 'bg-warning',
            'High' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get type badge class - FIXED VERSION
     */
    public function getTypeBadgeAttribute()
    {
        return $this->type === 'External' ? 'bg-info' : 'bg-success';
    }

    /**
     * Get category badge class - FIXED VERSION
     */
    public function getCategoryBadgeAttribute()
    {
        return $this->category === 'Risk' ? 'bg-danger' : 'bg-primary';
    }

    // Rest of the methods remain the same...
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
