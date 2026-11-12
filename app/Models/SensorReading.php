<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    public $timestamps = false;

    protected $guarded = [];
    const UPDATED_AT = null; 

    protected $casts = [
        'temperature' => 'float',
        'humidity'    => 'float',
        'created_at'  => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
