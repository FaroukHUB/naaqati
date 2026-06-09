<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Bundle extends Model
{
    use HasSlug, SoftDeletes;

    protected $fillable = ['nom', 'slug', 'desc_courte', 'prix_special', 'devise_code', 'actif'];

    protected $casts = [
        'actif' => 'boolean',
        'prix_special' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('nom')->saveSlugsTo('slug');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BundleItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'bundle_items')
            ->withPivot('quantite')
            ->withTimestamps();
    }
}
