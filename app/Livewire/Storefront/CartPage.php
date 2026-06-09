<?php

namespace App\Livewire\Storefront;

use App\Models\Packaging;
use App\Services\CartService;
use App\Support\CurrentRelais;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.shop-layout')]
class CartPage extends Component
{
    /** Destinataire et message par clé de paquet. */
    public array $dest = [];
    public array $msg = [];

    public function mount(CartService $cart): void
    {
        $defaut = $this->defautEmballageId();

        foreach ($cart->packages() as $p) {
            $this->dest[$p['key']] = $p['destinataire'] ?? '';
            $this->msg[$p['key']] = $p['message'] ?? '';

            // Emballage par défaut (le moins cher) si aucun choisi.
            if ($p['packaging_id'] === null && $defaut) {
                $cart->setPackaging($p['key'], $defaut);
            }
        }
    }

    public function changeQuantite(string $key, int $productId, int $delta, CartService $cart): void
    {
        $cart->add($productId, $delta, $key);
    }

    public function supprimer(string $key, int $productId, CartService $cart): void
    {
        $cart->removeItem($key, $productId);
    }

    public function deplacer(int $productId, string $from, string $to, CartService $cart): void
    {
        $cart->moveItem($productId, $from, $to);
    }

    public function ajouterPaquet(CartService $cart): void
    {
        $key = $cart->addPackage();
        $this->dest[$key] = '';
        $this->msg[$key] = '';

        if ($defaut = $this->defautEmballageId()) {
            $cart->setPackaging($key, $defaut);
        }
    }

    public function supprimerPaquet(string $key, CartService $cart): void
    {
        $cart->removePackage($key);
        unset($this->dest[$key], $this->msg[$key]);
    }

    public function choisirEmballage(string $key, ?int $packagingId, CartService $cart): void
    {
        $cart->setPackaging($key, $packagingId ?: null);
    }

    public function updatedDest($value, $key): void
    {
        app(CartService::class)->setInfos($key, $value ?: null, $this->msg[$key] ?? null);
    }

    public function updatedMsg($value, $key): void
    {
        app(CartService::class)->setInfos($key, $this->dest[$key] ?? null, $value ?: null);
    }

    public function commander()
    {
        return redirect()->route('shop.checkout');
    }

    private function defautEmballageId(): ?int
    {
        $relaisId = app(CurrentRelais::class)->id();

        return Packaging::where('actif', true)
            ->where(fn ($q) => $q->whereNull('relais_id')->orWhere('relais_id', $relaisId))
            ->orderBy('prix')
            ->value('id');
    }

    public function render(CartService $cart, CurrentRelais $relais)
    {
        $relaisId = $relais->id();

        $emballages = Packaging::where('actif', true)
            ->where(fn ($q) => $q->whereNull('relais_id')->orWhere('relais_id', $relaisId))
            ->with('media')
            ->orderBy('prix')
            ->get();

        return view('livewire.storefront.cart-page', [
            'packages' => $cart->packages(),
            'emballages' => $emballages,
            'sousTotal' => $cart->sousTotal(),
            'frais' => $cart->fraisTotal(),
            'total' => $cart->total(),
        ]);
    }
}
