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
    public string $email = '';
    public string $recuperateur = '';
    public string $commentaire = '';

    /** Options « qui récupère » : clé => libellé. */
    public array $recuperateurOptions = [
        'moi' => 'Moi-même',
        'femme' => 'Une femme de la famille',
        'enfant' => 'Un enfant (garçon non pubère ou fille)',
        'homme' => 'Un homme (mari, proche)',
    ];

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
            $this->telephone = $c->telephone;
            $this->email = (string) $c->email;
        }
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

    public function selectRecuperateur(string $key): void
    {
        $this->recuperateur = $key;
    }

    public function valider(CheckoutService $checkout, PickupService $pickup, CurrentRelais $relais)
    {
        $this->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'telephone' => ['required', 'string', 'min:6', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'recuperateur' => ['required', 'in:' . implode(',', array_keys($this->recuperateurOptions))],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ], messages: [
            'recuperateur.required' => 'Merci d\'indiquer qui viendra récupérer.',
            'recuperateur.in' => 'Merci d\'indiquer qui viendra récupérer.',
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

        $telephone = auth('customer')->check()
            ? auth('customer')->user()->telephone
            : Phone::international($this->indicatif, $this->telephone);

        try {
            $order = $checkout->passerCommande([
                'nom' => $this->nom,
                'telephone' => $telephone,
                'email' => $this->email ?: null,
                'date_retrait' => $dateFinale,
                'creneau' => $creneauFinal,
                'recuperateur' => $this->recuperateurOptions[$this->recuperateur] ?? null,
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
