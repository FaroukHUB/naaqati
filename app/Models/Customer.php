<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'nom', 'telephone', 'email', 'notes',
        'total_depense', 'nb_commandes', 'derniere_commande_at',
    ];

    protected $casts = [
        'total_depense' => 'integer',
        'derniere_commande_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function feedbackResponses(): HasMany
    {
        return $this->hasMany(FeedbackResponse::class);
    }
}
