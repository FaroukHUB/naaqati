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
        'date_retrait', 'creneau_retrait', 'recuperateur', 'a_l_appoint', 'paie_avec', 'commentaire',
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
        'a_l_appoint' => 'boolean',
        'paie_avec' => 'integer',
    ];

    /** Monnaie à rendre (centimes), si la cliente n'a pas l'appoint. */
    public function monnaieARendre(): int
    {
        if ($this->a_l_appoint || ! $this->paie_avec) {
            return 0;
        }

        return max(0, $this->paie_avec - $this->total);
    }

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

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class)->orderBy('position');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
