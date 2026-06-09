<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderPackage extends Model
{
    protected $fillable = [
        'order_id', 'packaging_id', 'nom_destinataire', 'message_cadeau', 'frais_emballage', 'position',
    ];

    protected $casts = [
        'frais_emballage' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function packaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Libellé d'affichage : "Coffret cadeau · pour Maman". */
    public function libelle(): string
    {
        $emb = $this->packaging?->nom ?? 'Sachet kraft';

        return $this->nom_destinataire
            ? "{$emb} · pour {$this->nom_destinataire}"
            : $emb;
    }
}
