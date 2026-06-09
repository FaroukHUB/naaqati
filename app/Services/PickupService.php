<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PickupSchedule;
use App\Models\ScheduleException;
use Carbon\Carbon;

/**
 * Calcule les dates et créneaux de retrait disponibles pour un relais,
 * en tenant compte des horaires, des jours/heures bloqués et de la capacité.
 *
 * Règle V1 : retrait possible d'aujourd'hui jusqu'à 7 jours à l'avance.
 */
class PickupService
{
    private const JOURS_MAX = 7;

    /**
     * Retourne les créneaux disponibles par date.
     *
     * @return array<string, array<int,string>>  ["2026-06-10" => ["matin","apres_midi"], ...]
     */
    public function creneauxDisponibles(int $relaisId): array
    {
        $resultat = [];
        $debut = Carbon::today();

        for ($i = 0; $i <= self::JOURS_MAX; $i++) {
            $date = $debut->copy()->addDays($i);
            $creneaux = $this->creneauxPourDate($relaisId, $date);

            if (! empty($creneaux)) {
                $resultat[$date->toDateString()] = array_values($creneaux);
            }
        }

        return $resultat;
    }

    /** Créneaux ouverts pour une date donnée (après filtrage blocages + capacité). */
    public function creneauxPourDate(int $relaisId, Carbon $date): array
    {
        $schedule = PickupSchedule::withoutGlobalScopes()
            ->where('relais_id', $relaisId)
            ->where('jour_semaine', $date->dayOfWeek)
            ->where('actif', true)
            ->first();

        if (! $schedule) {
            return [];
        }

        // Journée entière bloquée ?
        $journeeBloquee = ScheduleException::withoutGlobalScopes()
            ->where('relais_id', $relaisId)
            ->whereDate('date', $date)
            ->whereNull('creneau')
            ->exists();

        if ($journeeBloquee) {
            return [];
        }

        $creneaux = $schedule->creneaux;

        // Retirer les créneaux explicitement bloqués.
        $bloques = ScheduleException::withoutGlobalScopes()
            ->where('relais_id', $relaisId)
            ->whereDate('date', $date)
            ->whereNotNull('creneau')
            ->pluck('creneau')
            ->all();

        $creneaux = array_diff($creneaux, $bloques);

        // Retirer les créneaux pleins (capacité atteinte).
        if ($schedule->capacite_max !== null) {
            $creneaux = array_filter($creneaux, function ($creneau) use ($relaisId, $date, $schedule) {
                $nb = Order::withoutGlobalScopes()
                    ->where('relais_id', $relaisId)
                    ->whereDate('date_retrait', $date)
                    ->where('creneau_retrait', $creneau)
                    ->whereNotIn('statut', ['annulee'])
                    ->count();

                return $nb < $schedule->capacite_max;
            });
        }

        return array_values($creneaux);
    }

    public function estDisponible(int $relaisId, Carbon $date, string $creneau): bool
    {
        return in_array($creneau, $this->creneauxPourDate($relaisId, $date), true);
    }
}
