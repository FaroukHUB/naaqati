<?php

namespace App\Livewire\Account;

use App\Models\Product;
use App\Support\CurrentRelais;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.shop-layout')]
class Dashboard extends Component
{
    public string $nom = '';
    public string $email = '';
    public string $adresse = '';
    public string $ville = '';
    public bool $newsletter = false;

    public function mount(): void
    {
        $c = Auth::guard('customer')->user();
        $this->nom = $c->nom;
        $this->email = (string) $c->email;
        $this->adresse = (string) $c->adresse;
        $this->ville = (string) $c->ville;
        $this->newsletter = (bool) $c->newsletter;
    }

    public function enregistrer()
    {
        $this->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['nullable', 'email', 'max:190'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
        ]);

        Auth::guard('customer')->user()->update([
            'nom' => $this->nom,
            'email' => $this->email ?: null,
            'adresse' => $this->adresse ?: null,
            'ville' => $this->ville ?: null,
            'newsletter' => $this->newsletter,
        ]);

        $this->dispatch('flash', message: 'Informations enregistrées ✓');
    }

    public function render(CurrentRelais $relais)
    {
        $customer = Auth::guard('customer')->user();

        $commandes = $customer->orders()
            ->with('items')
            ->latest()
            ->take(10)
            ->get();

        $relaisId = $relais->id();
        $nouveautes = Product::query()
            ->where('actif', true)
            ->whereHas('inventories', fn ($q) => $q->where('relais_id', $relaisId)->where('actif', true))
            ->with(['inventories' => fn ($q) => $q->where('relais_id', $relaisId), 'media'])
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.account.dashboard', [
            'customer' => $customer,
            'commandes' => $commandes,
            'nouveautes' => $nouveautes,
            'relaisId' => $relaisId,
        ]);
    }
}
