<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    public $timestamps = false;

    protected $guarded = [];

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
