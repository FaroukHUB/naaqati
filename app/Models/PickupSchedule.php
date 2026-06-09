<?php

namespace App\Models;

use App\Support\Scopes\BelongsToRelais;
use Illuminate\Database\Eloquent\Model;

class PickupSchedule extends Model
{
    use BelongsToRelais;

    protected $fillable = ['relais_id', 'jour_semaine', 'creneaux', 'capacite_max', 'actif'];

    protected $casts = [
        'creneaux' => 'array',
        'actif' => 'boolean',
    ];
}
