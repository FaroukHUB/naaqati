<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackForm extends Model
{
    protected $fillable = ['nom', 'product_id', 'category_id', 'schema', 'actif'];

    protected $casts = [
        'schema' => 'array',
        'actif' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class, 'form_id');
    }
}
