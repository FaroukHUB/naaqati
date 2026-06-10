<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\PickupService;
use App\Support\CurrentRelais;
use App\Support\Phone;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.shop-layout')]
class Checkout extends Component
{
    public string $nom = '';
    public string $indicatif = '213';
    public string $telephone = '';
    public string $contactGenre = '';
    public string $email = '';
    public bool $recupHomme = false;
    public bool $appoint = true;
    public string $paieAvec = '';
    public string $commentaire = '';

    // Date : 'date' (une date choisie) ou 'inconnue'
    public string $dateMode = 'date';
    public string $date_retrait = '';

    // Heure : 'creneau' | 'precise' | 'inconnue'
    public string $heureMode = 'creneau';
    public string $creneau = '';
    public string $heurePrecise = '';

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

        if ($c = auth('customer')->user()) {
            $this->nom = $c->nom;
            $this->email = (string) $c->email;
            [$this->indicatif, $this->telephone] = $this->splitPhone($c->telephone);
        }
    }

    /** Découpe un numéro stocké en [indicatif, numéro local]. */
    private function splitPhone(?string $full): array
    {
        $digits = preg_replace('/\D+/', '', (string) $full);
        $codes = array_keys(Phone::INDICATIFS);
        usort($codes, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($codes as $code) {
            if (str_starts_with($digits, $code) && strlen($digits) > strlen($code) + 5) {
                return [$code, substr($digits, strlen($code))];
            }
        }

        return ['213', ltrim($digits, '0')];
    }

    public function setDateMode(string $mode): void
    {
        $this->dateMode = $mode;
        if ($mode === 'inconnue') {
            $this->date_retrait = '';
        }
    }

    public function selectDate(string $date): void
    {
        $this->dateMode = 'date';
        $this->date_retrait = $date;
        $this->creneau = '';
    }

    public function setHeureMode(string $mode): void
    {
        $this->heureMode = $mode;
        $this->creneau = '';
        $this->heurePrecise = '';
    }

    public function selectCreneau(string $creneau): void
    {
        $this->heureMode = 'creneau';
        $this->creneau = $creneau;
    }

    public function valider(CheckoutService $checkout, PickupService $pickup, CurrentRelais $relais, CartService $cart)
    {
        $this->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'telephone' => ['required', 'string', 'min:6', 'max:30'],
            'contactGenre' => ['required', 'in:femme,homme'],
            'email' => ['nullable', 'email', 'max:190'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ], messages: [
            'contactGenre.required' => 'Merci d\'indiquer si ce numéro est celui d\'une femme ou d\'un homme.',
            'contactGenre.in' => 'Merci d\'indiquer si ce numéro est celui d\'une femme ou d\'un homme.',
        ], attributes: [
            'nom' => 'nom', 'telephone' => 'téléphone',
        ]);

        $dateFinale = null;
        $creneauFinal = null;

        if ($this->dateMode === 'date') {
            if ($this->date_retrait === '') {
                $this->addError('date_retrait', 'Choisissez une date ou « Je ne sais pas encore ».');
                return;
            }
            $dateFinale = $this->date_retrait;

            if ($this->heureMode === 'creneau') {
                $dispos = $pickup->creneauxDisponibles($relais->id());
                if ($this->creneau === '' || ! in_array($this->creneau, $dispos[$this->date_retrait] ?? [], true)) {
                    $this->addError('creneau', 'Choisissez un créneau disponible (ou une autre option d\'heure).');
                    return;
                }
                $creneauFinal = $this->creneau;
            } elseif ($this->heureMode === 'precise') {
                if (! preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $this->heurePrecise)) {
                    $this->addError('heurePrecise', 'Indiquez une heure valide (ex : 14:30).');
                    return;
                }
                $creneauFinal = $this->heurePrecise;
            }
            // heureMode 'inconnue' => créneau null
        }
        // dateMode 'inconnue' => date et créneau null

        // Paiement : appoint ou montant avec lequel la cliente paiera.
        $paieAvecCentimes = null;
        if (! $this->appoint) {
            $total = $cart->total();
            $montant = (float) str_replace(',', '.', $this->paieAvec);
            if ($montant <= 0) {
                $this->addError('paieAvec', 'Indiquez le montant avec lequel vous paierez.');
                return;
            }
            $paieAvecCentimes = (int) round($montant * 100);
            if ($paieAvecCentimes < $total) {
                $this->addError('paieAvec', 'Le montant doit être au moins égal au total (' . number_format($total / 100, 0, ',', ' ') . ' DA).');
                return;
            }
        }

        $telephone = Phone::international($this->indicatif, $this->telephone);

        try {
            $order = $checkout->passerCommande([
                'nom' => $this->nom,
                'telephone' => $telephone,
                'customer_id' => auth('customer')->id(),
                'email' => $this->email ?: null,
                'date_retrait' => $dateFinale,
                'creneau' => $creneauFinal,
                'recuperateur' => $this->recupHomme
                    ? 'Homme / garçon pubère — dépôt un peu plus loin de la porte'
                    : 'Femme ou enfant — remise à la porte',
                'contact_genre' => $this->contactGenre,
                'a_l_appoint' => $this->appoint,
                'paie_avec' => $paieAvecCentimes,
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
