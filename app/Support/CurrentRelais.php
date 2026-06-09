<?php

namespace App\Support;

use App\Models\Relais;

/**
 * Contexte du point relais courant.
 *
 * En V1 il n'existe qu'un seul relais (Riadi City) : on résout le relais par
 * défaut. En V2/V3 (multi-relais), ce service sera alimenté par le middleware
 * SetCurrentRelais à partir du sous-domaine ou de la ville sélectionnée.
 */
class CurrentRelais
{
    protected ?Relais $relais = null;

    public function set(Relais $relais): void
    {
        $this->relais = $relais;
    }

    public function get(): ?Relais
    {
        if ($this->relais === null) {
            $this->relais = Relais::where('actif', true)->orderBy('id')->first();
        }

        return $this->relais;
    }

    public function id(): ?int
    {
        return $this->get()?->id;
    }

    public function devise(): string
    {
        return $this->get()?->devise_code ?? config('naaqati.devise_defaut', 'DZD');
    }
}
