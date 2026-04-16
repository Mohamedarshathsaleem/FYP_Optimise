<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeuActionItem extends Model
{
    use HasFactory;

    protected $table = 'seu_action_items';

    protected $fillable = [
        'seu_flagging_id',
        'action_description',
        'priority',
        'status',
        'target_date',
        'completion_date',
        'estimated_savings_gj',
        'actual_savings_gj',
        'notes',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completion_date' => 'date',
        'estimated_savings_gj' => 'decimal:4',
        'actual_savings_gj' => 'decimal:4',
    ];

    // Relationships
    public function seuFlagging()
    {
        return $this->belongsTo(SeuFlagging::class, 'seu_flagging_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('target_date')
            ->where('target_date', '<', now());
    }
}
