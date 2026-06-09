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
    public ?int $packagingId = null;

    public function mount(CartService $cart): void
    {
        $this->packagingId = $cart->packaging()?->id;
    }

    public function changerQuantite(int $productId, int $delta, CartService $cart): void
    {
        $cart->add($productId, $delta);
        $this->dispatch('cart-updated');
    }

    public function supprimer(int $productId, CartService $cart): void
    {
        $cart->remove($productId);
        $this->dispatch('cart-updated');
    }

    public function updatedPackagingId(CartService $cart): void
    {
        $cart->setPackaging($this->packagingId ?: null);
    }

    public function commander()
    {
        return redirect()->route('shop.checkout');
    }

    public function render(CartService $cart, CurrentRelais $relais)
    {
        $relaisId = $relais->id();

        $emballages = Packaging::where('actif', true)
            ->where(fn ($q) => $q->whereNull('relais_id')->orWhere('relais_id', $relaisId))
            ->orderBy('prix')
            ->get();

        return view('livewire.storefront.cart-page', [
            'lines' => $cart->lines(),
            'sousTotal' => $cart->sousTotal(),
            'frais' => $cart->fraisEmballage(),
            'total' => $cart->total(),
            'emballages' => $emballages,
        ]);
    }
}
