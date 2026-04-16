<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'committee_id',
        'name',
        'position',
        'start_date',
        'end_date',
        'role',
        'department',
        'communication_method',
        'responsibilities'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Generate unique committee ID
     */
    public static function generateCommitteeId()
    {
        $lastCommittee = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastCommittee ? intval(substr($lastCommittee->committee_id, 3)) + 1 : 1;
        return 'EMC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get appointment period in months
     */
    public function getAppointmentPeriodAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 'N/A';
        }

        $months = $this->start_date->diffInMonths($this->end_date);
        return $months . ' Month' . ($months > 1 ? 's' : '');
    }

    /**
     * Get formatted start date
     */
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d M Y') : 'N/A';
    }

    /**
     * Get formatted end date
     */
    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('d M Y') : 'N/A';
    }

    /**
     * Get responsibilities as array
     */
    public function getResponsibilitiesArrayAttribute()
    {
        return explode("\n", $this->responsibilities);
    }

    /**
     * Check if committee member is active
     */
    public function getIsActiveAttribute()
    {
        $today = Carbon::now();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    /**
     * Scope for active committees
     */
    public function scopeActive($query)
    {
        $today = Carbon::now();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    /**
     * Scope for specific role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for specific department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }
}
