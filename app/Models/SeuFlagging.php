<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeuFlagging extends Model
{
    use HasFactory;

    protected $table = 'seu_flagging';

    protected $fillable = [
        'year',
        'criteria_id',
        'seu_name',
        'energy_type',
        'current_gj',
        'overall_usage_pct',
        'enpi_reference',
        'is_flagged',
        'is_manually_overridden',
        'override_reason',
        'load_apportioning_id',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'current_gj' => 'decimal:4',
        'overall_usage_pct' => 'decimal:4',
        'is_flagged' => 'boolean',
        'is_manually_overridden' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function criteria()
    {
        return $this->belongsTo(SeuCriteria::class, 'criteria_id');
    }

    public function loadApportioning()
    {
        return $this->belongsTo(LoadApportioning::class, 'load_apportioning_id');
    }

    public function actionItems()
    {
        return $this->hasMany(SeuActionItem::class, 'seu_flagging_id');
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
    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByEnergyType($query, $type)
    {
        return $query->where('energy_type', $type);
    }

    public function scopeEnergySeus($query)
    {
        return $query->where('energy_type', 'energy');
    }

    public function scopeEnergyResourceSeus($query)
    {
        return $query->where('energy_type', 'energy_resource');
    }

    // Accessors
    public function getFormattedUsagePctAttribute()
    {
        return number_format($this->overall_usage_pct * 100, 2) . '%';
    }

    public function getFormattedCurrentGjAttribute()
    {
        return number_format($this->current_gj, 2);
    }

    // Methods
    public function toggleFlag($reason = null)
    {
        $this->is_flagged = !$this->is_flagged;
        $this->is_manually_overridden = true;
        $this->override_reason = $reason;
        $this->updated_by = auth()->id();
        $this->save();
    }
}
