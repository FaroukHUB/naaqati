<?php

namespace App\Models;

use App\Support\Scopes\BelongsToRelais;
use Illuminate\Database\Eloquent\Model;

class ScheduleException extends Model
{
    use BelongsToRelais;

    protected $fillable = ['relais_id', 'date', 'creneau', 'type', 'motif'];

    protected $casts = [
        'date' => 'date',
    ];
}
