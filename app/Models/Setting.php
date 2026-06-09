<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = ['cle', 'valeur', 'relais_id'];

    public function relais(): BelongsTo
    {
        return $this->belongsTo(Relais::class);
    }

    /** Lecture d'un réglage (global par défaut, ou spécifique relais). */
    public static function get(string $cle, mixed $defaut = null, ?int $relaisId = null): mixed
    {
        return static::where('cle', $cle)
            ->where('relais_id', $relaisId)
            ->value('valeur') ?? $defaut;
    }
}
