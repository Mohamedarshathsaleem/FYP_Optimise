<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stakeholder extends Model
{
    use HasFactory;

    protected $fillable = [
        'stakeholder_id',
        'name',
        'type',
        'role',
        'needs_expectations',
        'influence_level',
        'communication_method',
        'engagement_frequency',
        'responsible_person',
        'status',
        'remarks'
    ];

    // Generate unique stakeholder ID
    public static function generateStakeholderId()
    {
        $lastStakeholder = self::orderBy('id', 'desc')->first();
        if (!$lastStakeholder) {
            return 'ST-001';
        }

        $lastNumber = (int) substr($lastStakeholder->stakeholder_id, 3);
        $newNumber = $lastNumber + 1;

        return 'ST-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
