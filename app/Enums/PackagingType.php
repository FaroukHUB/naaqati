<?php

namespace App\Enums;

enum PackagingType: string
{
    case Sachet = 'sachet';
    case Boite = 'boite';
    case Coffret = 'coffret';

    public function label(): string
    {
        return match ($this) {
            self::Sachet => 'Sachet kraft',
            self::Boite => 'Boîte cadeau',
            self::Coffret => 'Coffret cadeau',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $t) => [$t->value => $t->label()]
        )->all();
    }
}
