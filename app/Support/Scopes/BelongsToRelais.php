<?php

namespace App\Support\Scopes;

use App\Models\Relais;
use App\Support\CurrentRelais;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait à appliquer sur tout modèle transactionnel scopé par relais
 * (inventories, orders, pickup_schedules, schedule_exceptions, ...).
 *
 * - applique le RelaisScope global (filtrage automatique)
 * - remplit relais_id automatiquement à la création si absent
 * - fournit la relation relais()
 */
trait BelongsToRelais
{
    public static function bootBelongsToRelais(): void
    {
        static::addGlobalScope(new RelaisScope);

        static::creating(function ($model) {
            if (empty($model->relais_id) && app()->bound(CurrentRelais::class)) {
                $model->relais_id = app(CurrentRelais::class)->id();
            }
        });
    }

    public function relais(): BelongsTo
    {
        return $this->belongsTo(Relais::class);
    }
}
