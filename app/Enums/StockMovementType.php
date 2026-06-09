<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Entree = 'entree';
    case Sortie = 'sortie';
    case Reservation = 'reservation';
    case Liberation = 'liberation';
    case Ajustement = 'ajustement';
    case Vente = 'vente';

    public function label(): string
    {
        return match ($this) {
            self::Entree => 'Entrée stock',
            self::Sortie => 'Sortie stock',
            self::Reservation => 'Réservation',
            self::Liberation => 'Libération',
            self::Ajustement => 'Ajustement manuel',
            self::Vente => 'Vente',
        };
    }
}
