<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreenAccessLog extends Model
{
    protected $fillable = [
        'screen_id',
        'ip_address',
        'user_agent',
        'device_type',
        'logged_date',
    ];

    public function screen(): BelongsTo
    {
        return $this->belongsTo(Screen::class);
    }
}