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
        // Jika last_seen_at null, pasti offline
        if (!$this->last_seen_at) {
            return false;
        }

        // Cek apakah last_seen_at dalam 60 detik terakhir (lebih longgar)
        // Gunakan Carbon untuk perbandingan yang akurat
        return $this->last_seen_at->diffInSeconds(now()) < 60;
    }
    }
