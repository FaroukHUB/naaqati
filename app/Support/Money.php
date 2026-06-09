<?php

namespace App\Support;

/**
 * Tous les montants sont stockés en entiers (centimes) pour éviter les erreurs
 * de virgule flottante. Cette classe centralise les conversions d'affichage.
 */
class Money
{
    /** Centimes -> chaîne formatée. Ex: 125000, 'DZD' => "1 250,00 DZD" */
    public static function format(int $centimes, string $devise = 'DZD', int $decimales = 2): string
    {
        $montant = $centimes / (10 ** $decimales);
        $formate = number_format($montant, $decimales, ',', ' ');

        return "{$formate} {$devise}";
    }

    /** Saisie utilisateur (ex: "1250.50") -> centimes (125050). */
    public static function toCents(float|string $montant, int $decimales = 2): int
    {
        return (int) round(((float) $montant) * (10 ** $decimales));
    }
}
