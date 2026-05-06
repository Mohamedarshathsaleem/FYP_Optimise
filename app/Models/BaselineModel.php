<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BaselineModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_name',
        'number_of_independent_variables',
        'r_squared',
        'equation',
        'correlation_strength',
        'year',
        'dependent_variable',
        'dependent_variable_type',
        'energy_data_id',
        'energy_resource_id',
        'independent_variable_x1',
        'independent_variable_type_x1',
        'monthly_production_id_x1',
        'monthly_variable_id_x1',
        'independent_variable_x2',
        'independent_variable_type_x2',
        'monthly_production_id_x2',
        'monthly_variable_id_x2',
        'independent_variable_x3',
        'independent_variable_type_x3',
        'monthly_production_id_x3',
        'monthly_variable_id_x3',
        'independent_variable_x4',
        'independent_variable_type_x4',
        'monthly_production_id_x4',
        'monthly_variable_id_x4',
        'approval_status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'r_squared' => 'float',
        'number_of_independent_variables' => 'integer',
        'year' => 'integer',
    ];

    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function getCorrelationBadgeColorAttribute()
    {
        switch ($this->correlation_strength) {
            case 'Very Strong': return 'success';
            case 'Strong':      return 'primary';
            case 'Moderate':    return 'info';
            case 'Weak':        return 'warning';
            default:            return 'secondary';
        }
    }

    public function getFormattedEquationAttribute()
    {
        return str_replace(
            ['x1', 'x2', 'x3', 'x4'],
            ['X₁', 'X₂', 'X₃', 'X₄'],
            $this->equation
        );
    }

    public function getApprovalBadgeColorAttribute(): string
    {
        return match($this->approval_status) {
            'approved'    => 'success',
            'disapproved' => 'danger',
            default       => 'secondary',
        };
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function energyData()
    {
        return $this->belongsTo(EnergyData::class);
    }

    public function energyResource()
    {
        return $this->belongsTo(EnergyResourceData::class, 'energy_resource_id');
    }

    public function monthlyProductionX1()
    {
        return $this->belongsTo(MonthlyProduction::class, 'monthly_production_id_x1');
    }

    public function monthlyVariableX1()
    {
        return $this->belongsTo(MonthlyVariable::class, 'monthly_variable_id_x1');
    }

    public function monthlyProductionX2()
    {
        return $this->belongsTo(MonthlyProduction::class, 'monthly_production_id_x2');
    }

    public function monthlyVariableX2()
    {
        return $this->belongsTo(MonthlyVariable::class, 'monthly_variable_id_x2');
    }

    public function monthlyProductionX3()
    {
        return $this->belongsTo(MonthlyProduction::class, 'monthly_production_id_x3');
    }

    public function monthlyVariableX3()
    {
        return $this->belongsTo(MonthlyVariable::class, 'monthly_variable_id_x3');
    }

    public function monthlyProductionX4()
    {
        return $this->belongsTo(MonthlyProduction::class, 'monthly_production_id_x4');
    }

    public function monthlyVariableX4()
    {
        return $this->belongsTo(MonthlyVariable::class, 'monthly_variable_id_x4');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Human-readable label for the dependent variable.
     */
    public function getDependentLabelAttribute(): string
    {
        if ($this->dependent_variable_type === 'energy_data' && $this->energyData) {
            return $this->energyData->energy_type . ' (' . $this->energyData->provider . ')';
        }

        if ($this->dependent_variable_type === 'energy_resource' && $this->energyResource) {
            return $this->energyResource->resource_type . ' (' . $this->energyResource->provider . ')';
        }

        return $this->dependent_variable ?? '—';
    }
}
