<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scope extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope_id',
        'included',
        'excluded',
        'status',
        'rationale_for_excluding'
    ];

    public static function generateScopeId()
    {
        $lastScope = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastScope ? intval(substr($lastScope->scope_id, 3)) + 1 : 1;
        return 'SCP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
