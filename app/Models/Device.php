<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    // WAJIB: Agar 'is_online' ikut terkirim saat di-convert ke JSON
    protected $appends = ['is_online'];

    public function getIsOnlineAttribute(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subSeconds(30));
    }

    public function leds()
    {
        return $this->hasMany(Led::class)->orderBy('number');
    }
}
