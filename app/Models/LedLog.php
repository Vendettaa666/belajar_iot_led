<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedLog extends Model
{
    public $timestamps = false;   // hanya punya created_at (diisi database)

    protected $guarded = [];

    protected $casts = [
        'is_on'      => 'boolean',
        'created_at' => 'datetime',
    ];

    public function led()
    {
        return $this->belongsTo(Led::class);
    }
}
