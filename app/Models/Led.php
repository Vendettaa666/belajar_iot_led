<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Led extends Model
{
    protected $guarded = [];

    protected $casts = [
        'target_on' => 'boolean',
        'is_on'     => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function logs()
    {
        return $this->hasMany(LedLog::class);
    }
}
