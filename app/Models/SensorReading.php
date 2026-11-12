<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $guarded = [];

    // Tabel ini hanya mencatat waktu dibuat, tidak ada updated_at
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
