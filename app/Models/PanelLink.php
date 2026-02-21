<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PanelLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'panel_id',
        'title',
        'url',
        'status',
        'duration_time',
        'display_number',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PanelLink $link) {
            if (empty($link->display_number)) {
                $link->display_number = static::where('panel_id', $link->panel_id)->max('display_number') + 1;
            }
        });
    }

    public function panel(): BelongsTo
    {
        return $this->belongsTo(Panel::class);
    }
}
