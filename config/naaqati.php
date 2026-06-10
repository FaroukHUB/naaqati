<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Réglages métier Naaqati
    |--------------------------------------------------------------------------
    | Valeurs par défaut de la phase 1 (mono-relais Riadi City).
    | Beaucoup deviendront configurables par relais en V3 via la table settings.
    */

    'devise_defaut' => env('NAAQATI_DEVISE', 'DZD'),

    // Nombre maximum de jours à l'avance pour choisir une date de retrait.
    'retrait_jours_max' => 7,

    // Délais de suivi après achat (en jours) proposés en V2.
    'suivi_delais' => [15, 30, 45],

    // Slug du relais par défaut (phase 1).
    'relais_defaut_slug' => 'riadi-city',

    // Email qui reçoit les notifications de commande (côté boutique).
    'admin_email' => env('NAAQATI_ADMIN_EMAIL'),
];
