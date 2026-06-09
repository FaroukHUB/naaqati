<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nom', 'telephone', 'email', 'password', 'adresse', 'ville',
        'newsletter', 'notes', 'total_depense', 'nb_commandes', 'derniere_commande_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'total_depense' => 'integer',
        'newsletter' => 'boolean',
        'derniere_commande_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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
