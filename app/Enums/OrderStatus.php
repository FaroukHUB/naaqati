<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Recue = 'recue';
    case EnPreparation = 'en_preparation';
    case Prete = 'prete';
    case Recuperee = 'recuperee';
    case Terminee = 'terminee';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::Recue => 'Commande reçue',
            self::EnPreparation => 'En préparation',
            self::Prete => 'Prête à récupérer',
            self::Recuperee => 'Récupérée',
            self::Terminee => 'Terminée',
            self::Annulee => 'Annulée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Recue => 'gray',
            self::EnPreparation => 'warning',
            self::Prete => 'info',
            self::Recuperee => 'success',
            self::Terminee => 'success',
            self::Annulee => 'danger',
        };
    }

    /** Statuts qui réservent du stock (en attente de retrait). */
    public function reservesStock(): bool
    {
        return in_array($this, [self::Recue, self::EnPreparation, self::Prete], true);
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $s) => [$s->value => $s->label()]
        )->all();
    }
}
