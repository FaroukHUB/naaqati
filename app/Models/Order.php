<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Support\Scopes\BelongsToRelais;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use BelongsToRelais;

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->numero)) {
                $order->numero = 'NQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    protected $fillable = [
        'numero', 'customer_id', 'relais_id', 'statut', 'devise_code',
        'sous_total', 'packaging_id', 'frais_emballage', 'total',
        'date_retrait', 'creneau_retrait', 'commentaire',
    ];

    protected $attributes = [
        'statut' => 'recue',
    ];

    protected $casts = [
        'statut' => OrderStatus::class,
        'date_retrait' => 'date',
        'sous_total' => 'integer',
        'frais_emballage' => 'integer',
        'total' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function packaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
