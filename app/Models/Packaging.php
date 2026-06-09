<?php

namespace App\Models;

use App\Enums\PackagingType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Packaging extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

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
