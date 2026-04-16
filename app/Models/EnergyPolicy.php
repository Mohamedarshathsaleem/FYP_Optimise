<?php
// app/Models/EnergyPolicy.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company_name',
        'company_logo',
        'policy_statement',
        'energy_standard',
        'document_path',
        'commitments',
        'summary',
        'policy_completed',
        'date_completed',
        'date_approved',
        'who_approved',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'policy_completed' => 'boolean',
        'date_completed' => 'date',
        'date_approved' => 'date',
        'commitments' => 'array'
    ];

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getCommitmentsArrayAttribute()
    {
        if (is_string($this->commitments)) {
            return json_decode($this->commitments, true) ?? [];
        }
        return $this->commitments ?? [];
    }
}
