<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function leds()
    {
        return $this->hasMany(Led::class)->orderBy('number');
    }

    public function readings()
    {
        return $this->hasMany(SensorReading::class);
    }

    // Dianggap online kalau ESP menghubungi server dalam 30 detik terakhir
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subSeconds(30));
    }
}
