<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    // 1. Tambahkan baris ini agar 'is_online' otomatis masuk ke JSON
    protected $appends = ['is_online'];

    // 2. Ubah method isOnline() menjadi Accessor (getIsOnlineAttribute)
    public function getIsOnlineAttribute(): bool
    {
        // Dianggap online kalau ESP menghubungi server dalam 30 detik terakhir
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subSeconds(30));
    }

    public function leds(): HasMany
    {
        return $this->hasMany(Led::class)->orderBy('number');
    }

    public function readings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }
}
