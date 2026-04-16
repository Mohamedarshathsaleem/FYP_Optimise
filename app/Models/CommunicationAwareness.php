<?php

// app/Models/CommunicationAwareness.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunicationAwareness extends Model
{
    protected $table = 'communication_awareness';

    protected $fillable = [
        'action_initiative',
        'type',
        'energy_message',
        'target_audience',
        'communication',
        'person_in_charge',
        'planned_date',
        'remarks',
    ];
}