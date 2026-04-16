<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EipFilterPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'filters',
        'is_system',
        'is_favorite',
        'share_token',
        'usage_count',
    ];

    protected $casts = [
        'filters' => 'array',
        'is_system' => 'boolean',
        'is_favorite' => 'boolean',
        'usage_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
