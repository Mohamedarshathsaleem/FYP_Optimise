<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadApportioning extends Model
{
    use HasFactory;

    protected $table = 'load_apportioning';

    protected $fillable = [
        'year',
        'approach_id',
        'seu_category',
        'submeter_reference',
        'equipment_type',
        'equipment_name',
        'equipment_remark',
        'electricity_load_gj',
        'electricity_load_pct',
        'ng_meter_reference',
        'ng_load_gj',
        'ng_load_pct',
        'total_energy_gj',
        'total_energy_pct',
        'calculation_remark',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'electricity_load_gj' => 'decimal:4',
        'electricity_load_pct' => 'decimal:4',
        'ng_load_gj' => 'decimal:4',
        'ng_load_pct' => 'decimal:4',
        'total_energy_gj' => 'decimal:4',
        'total_energy_pct' => 'decimal:4',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function approach()
    {
        return $this->belongsTo(LoadApportioningApproach::class, 'approach_id');
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
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeBySeuCategory($query, $category)
    {
        return $query->where('seu_category', $category);
    }

    public function scopeByApproach($query, $approachId)
    {
        return $query->where('approach_id', $approachId);
    }

    // Accessors
    public function getFormattedElectricityPctAttribute()
    {
        return number_format($this->electricity_load_pct * 100, 2) . '%';
    }

    public function getFormattedNgPctAttribute()
    {
        return number_format($this->ng_load_pct * 100, 2) . '%';
    }

    public function getFormattedTotalPctAttribute()
    {
        return number_format($this->total_energy_pct * 100, 2) . '%';
    }

    // Static Methods
    public static function getSeuCategories($year = null)
    {
        $query = self::select('seu_category')
            ->whereNotNull('seu_category')
            ->distinct();

        if ($year) {
            $query->where('year', $year);
        }

        return $query->pluck('seu_category');
    }

    public static function calculateTotals($year, $approachId = null)
    {
        $query = self::where('year', $year);

        if ($approachId) {
            $query->where('approach_id', $approachId);
        }

        return [
            'total_electricity_gj' => $query->sum('electricity_load_gj'),
            'total_ng_gj' => $query->sum('ng_load_gj'),
            'total_energy_gj' => $query->sum('total_energy_gj'),
        ];
    }

    // Auto-calculate percentages before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Calculate total energy
            $model->total_energy_gj = ($model->electricity_load_gj ?? 0) + ($model->ng_load_gj ?? 0);
        });
    }
}
