<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['country_id', 'nom'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function relais(): HasMany
    {
        return $this->hasMany(Relais::class);
    }
}
