<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadApportioningApproach extends Model
{
    protected $table = 'load_apportioning_approaches';

    protected $fillable = [
        'name',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function loadApportioningRows()
    {
        return $this->hasMany(LoadApportioning::class, 'approach_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
