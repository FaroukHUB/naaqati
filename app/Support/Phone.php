<?php

namespace App\Support;

/**
 * Aide à la gestion des numéros de téléphone au format international,
 * indispensable pour les liens WhatsApp (wa.me).
 */
class Phone
{
    /** Indicatifs proposés (clé = code pays sans +, valeur = libellé). */
    public const INDICATIFS = [
        '213' => '🇩🇿 +213',
        '33' => '🇫🇷 +33',
        '212' => '🇲🇦 +212',
        '216' => '🇹🇳 +216',
        '971' => '🇦🇪 +971',
        '966' => '🇸🇦 +966',
        '1' => '🇺🇸 +1',
        '44' => '🇬🇧 +44',
    ];

    /**
     * Construit un numéro international (sans +) à partir d'un indicatif
     * et d'un numéro local. Ex : ('213', '0555 12 34 56') => '213555123456'.
     */
    public static function international(string $indicatif, string $local): string
    {
        $digits = preg_replace('/\D+/', '', $local);
        $digits = ltrim($digits, '0');

        return preg_replace('/\D+/', '', $indicatif) . $digits;
    }
}
