<?php

namespace App\Models;

use App\Support\Scopes\BelongsToRelais;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use BelongsToRelais;

    protected $fillable = [
        'product_id', 'relais_id', 'stock_disponible', 'stock_reserve',
        'stock_vendu', 'prix_override', 'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'prix_override' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
