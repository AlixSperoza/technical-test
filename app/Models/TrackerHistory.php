<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackerHistory extends Model
{
    public $timestamps = false;

    protected $table = 'tracker_history';

    protected $casts = [
        'battery' => 'array',
        'sensors' => 'array',
    ];
}
