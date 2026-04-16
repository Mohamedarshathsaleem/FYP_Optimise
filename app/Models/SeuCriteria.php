<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeuCriteria extends Model
{
    use HasFactory;

    protected $table = 'seu_criteria';

    protected $fillable = [
        'year',
        'criteria_type',
        'upper_limit',
        'lower_limit',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'upper_limit' => 'decimal:4',
        'lower_limit' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function seuFlaggings()
    {
        return $this->hasMany(SeuFlagging::class, 'criteria_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    // Static Methods
    public static function getOrCreateForYear($year, $criteriaType = 'load_percentage')
    {
        return self::firstOrCreate(
            ['year' => $year, 'criteria_type' => $criteriaType],
            [
                'upper_limit' => 1.0000,
                'lower_limit' => 0.0500,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]
        );
    }
}
