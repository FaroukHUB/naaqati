<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Services\CartService;
use App\Support\CurrentRelais;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.shop-layout')]
class ProductDetail extends Component
{
    public Product $product;

    public int $quantite = 1;

    public function mount(string $slug, CurrentRelais $relais): void
    {
        $relaisId = $relais->id();

        $this->product = Product::query()
            ->where('slug', $slug)
            ->where('actif', true)
            ->whereHas('inventories', fn ($q) => $q->where('relais_id', $relaisId)->where('actif', true))
            ->with(['inventories' => fn ($q) => $q->where('relais_id', $relaisId), 'media', 'category'])
            ->firstOrFail();
    }

    public function changerQuantite(int $delta): void
    {
        $max = $this->stockDisponible();
        $this->quantite = max(1, min($max, $this->quantite + $delta));
    }

    public function ajouter(CartService $cart): void
    {
        $cart->add($this->product->id, $this->quantite);
        $this->dispatch('cart-updated');
        $this->dispatch('flash', message: $this->quantite . ' × ' . $this->product->nom . ' ajouté ✓');
    }

    public function stockDisponible(): int
    {
        return (int) ($this->product->inventories->first()?->stock_disponible ?? 0);
    }

    public function render(CurrentRelais $relais)
    {
        $relaisId = $relais->id();

        $similaires = Product::query()
            ->where('actif', true)
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->whereHas('inventories', fn ($q) => $q->where('relais_id', $relaisId)->where('actif', true))
            ->with(['inventories' => fn ($q) => $q->where('relais_id', $relaisId), 'media'])
            ->limit(6)
            ->get();

        return view('livewire.storefront.product-detail', [
            'dispo' => $this->stockDisponible(),
            'prix' => $this->product->prixPourRelais($relaisId),
            'similaires' => $similaires,
            'relaisId' => $relaisId,
        ]);
    }
}
