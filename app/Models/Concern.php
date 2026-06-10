<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Concern extends Model
{
    use HasSlug;

    protected $fillable = ['nom', 'slug', 'emoji', 'position', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('nom')->saveSlugsTo('slug');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'concern_product');
    }
}
