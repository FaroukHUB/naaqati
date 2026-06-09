<?php

namespace App\Models;

use App\Support\CurrentRelais;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasSlug, SoftDeletes;

    protected $fillable = [
        'nom', 'slug', 'desc_courte', 'desc_longue', 'category_id',
        'poids_grammes', 'prix_base', 'devise_code', 'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'prix_base' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('nom')->saveSlugsTo('slug');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /** Inventaire pour le relais courant (V1 = Riadi City). */
    public function inventoryForCurrentRelais(): ?Inventory
    {
        $relaisId = app(CurrentRelais::class)->id();

        return $this->inventories->firstWhere('relais_id', $relaisId)
            ?? $this->inventories()->where('relais_id', $relaisId)->first();
    }

    /** Prix effectif : override relais sinon prix de base. */
    public function prixPourRelais(?int $relaisId = null): int
    {
        $relaisId ??= app(CurrentRelais::class)->id();
        $inv = $this->inventories->firstWhere('relais_id', $relaisId);

        return $inv?->prix_override ?? $this->prix_base;
    }
}
