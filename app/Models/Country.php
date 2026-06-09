<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = ['nom', 'iso2', 'devise_code', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
