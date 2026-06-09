<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PickupService;
use App\Support\CurrentRelais;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.shop-layout')]
class Checkout extends Component
{
    public string $nom = '';
    public string $telephone = '';
    public string $email = '';
    public string $date_retrait = '';
    public string $creneau = '';
    public string $commentaire = '';

    public array $creneauxLabels = [
        'matin' => 'Matin',
        'apres_midi' => 'Après-midi',
        'soir' => 'Soir',
    ];

    public function mount(CartService $cart)
    {
        if ($cart->isEmpty()) {
            return redirect()->route('shop.catalog');
        }

        // Pré-remplissage si la cliente est connectée.
        if ($c = auth('customer')->user()) {
            $this->nom = $c->nom;
            $this->telephone = $c->telephone;
            $this->email = (string) $c->email;
        }
    }

    public function selectDate(string $date): void
    {
        $this->date_retrait = $date;
        $this->creneau = '';
    }

    public function selectCreneau(string $creneau): void
    {
        $this->creneau = $creneau;
    }

    public function valider(CheckoutService $checkout, PickupService $pickup, CurrentRelais $relais)
    {
        $dispos = $pickup->creneauxDisponibles($relais->id());

        $this->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'telephone' => ['required', 'string', 'min:6', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'date_retrait' => ['required', 'date'],
            'creneau' => ['required', 'string'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ], attributes: [
            'nom' => 'nom', 'telephone' => 'téléphone', 'date_retrait' => 'date de retrait', 'creneau' => 'créneau',
        ]);

        // Sécurité : le créneau choisi doit être réellement disponible.
        if (! in_array($this->creneau, $dispos[$this->date_retrait] ?? [], true)) {
            $this->addError('creneau', 'Ce créneau n\'est plus disponible, merci d\'en choisir un autre.');
            return;
        }

        try {
            $order = $checkout->passerCommande([
                'nom' => $this->nom,
                'telephone' => $this->telephone,
                'email' => $this->email ?: null,
                'date_retrait' => $this->date_retrait,
                'creneau' => $this->creneau,
                'commentaire' => $this->commentaire ?: null,
            ]);
        } catch (\Throwable $e) {
            $this->addError('nom', $e->getMessage());
            return;
        }

        return redirect()->route('shop.confirmation', $order->numero);
    }

    public function render(CartService $cart, PickupService $pickup, CurrentRelais $relais)
    {
        return view('livewire.storefront.checkout', [
            'packages' => $cart->packages(),
            'total' => $cart->total(),
            'dates' => $pickup->creneauxDisponibles($relais->id()),
        ]);
    }
}
