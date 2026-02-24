<?php

namespace App\Models;

use App\Events\PanelUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Panel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'panel_group_id',
        'title',
        'status',
        'show_controls',
        'hash',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'show_controls' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Panel $panel) {
            if (empty($panel->hash)) {
                $panel->hash = (string) Str::uuid();
            }
        });

        static::saved(function (Panel $panel) {
            broadcast(new PanelUpdated($panel));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function panelGroup(): BelongsTo
    {
        return $this->belongsTo(PanelGroup::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(PanelLink::class)->orderBy('display_number');
    }

    public function allowedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'panel_user');
    }
}
