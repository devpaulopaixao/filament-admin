<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPanelShield;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function panelGroups(): HasMany
    {
        return $this->hasMany(PanelGroup::class);
    }

    public function allowedPanelGroups(): BelongsToMany
    {
        return $this->belongsToMany(PanelGroup::class, 'panel_group_user');
    }

    public function panels(): HasMany
    {
        return $this->hasMany(Panel::class);
    }

    public function allowedPanels(): BelongsToMany
    {
        return $this->belongsToMany(Panel::class, 'panel_user');
    }

    public function screens(): HasMany
    {
        return $this->hasMany(Screen::class);
    }

    public function allowedScreens(): BelongsToMany
    {
        return $this->belongsToMany(Screen::class, 'screen_user');
    }
}
