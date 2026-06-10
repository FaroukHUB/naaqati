<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Relais extends Model
{
    protected $table = 'relais';

    protected $fillable = [
        'city_id', 'nom', 'slug', 'adresse', 'batiment', 'code_portail',
        'code_porte', 'code_ascenseur', 'etage', 'instructions_acces',
        'telephone', 'devise_code', 'actif', 'settings',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'settings' => 'array',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function pickupSchedules(): HasMany
    {
        return $this->hasMany(PickupSchedule::class);
    }
}
