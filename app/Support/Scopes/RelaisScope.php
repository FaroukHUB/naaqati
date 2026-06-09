<?php

namespace App\Support\Scopes;

use App\Support\CurrentRelais;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global Scope multi-relais.
 *
 * Toute requête sur un modèle "relais-aware" est automatiquement filtrée par
 * le relais courant. En V1 il n'y a qu'un relais, donc le filtre est neutre,
 * mais le code est déjà prêt pour N relais sans refonte.
 *
 * Pour requêter tous les relais (admin central), utiliser ->withoutGlobalScope(RelaisScope::class).
 */
class RelaisScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Pas de filtrage côté admin central / CLI tant qu'aucun relais courant n'est défini explicitement.
        if (! app()->bound('request')) {
            return;
        }

        $relaisId = app(CurrentRelais::class)->id();

        if ($relaisId !== null) {
            $builder->where($model->getTable() . '.relais_id', $relaisId);
        }
    }
}
