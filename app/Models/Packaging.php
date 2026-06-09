<?php

namespace App\Models;

use App\Enums\PackagingType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packaging extends Model
{
    use SoftDeletes;

    protected $fillable = ['nom', 'type', 'prix', 'devise_code', 'couleur', 'relais_id', 'actif'];

    protected $casts = [
        'type' => PackagingType::class,
        'actif' => 'boolean',
        'prix' => 'integer',
    ];

    public function relais(): BelongsTo
    {
        return $this->belongsTo(Relais::class);
    }
}
