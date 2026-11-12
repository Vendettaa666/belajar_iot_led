<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    // WAJIB: Agar 'is_online' otomatis masuk ke JSON saat dikirim ke frontend
    protected $appends = ['is_online'];

    public function leds()
    {
        return $this->hasMany(Led::class)->orderBy('number');
    }

    // INI YANG TADI HILANG / BELUM ADA DI SERVER KAMU
    public function readings()
    {
        return $this->hasMany(SensorReading::class);
    }

    // Accessor untuk mengecek apakah device online (dalam 30 detik terakhir)
    public function getIsOnlineAttribute(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subSeconds(30));
    }
}
