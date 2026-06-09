<?php

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use App\Support\CurrentRelais;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.shop-layout')]
class Catalog extends Component
{
    #[Url(as: 'cat')]
    public ?int $categoryId = null;

    #[Url(as: 'q')]
    public string $search = '';

    public function ajouter(int $productId, CartService $cart): void
    {
        $cart->add($productId);
        $this->dispatch('cart-updated');
        $this->dispatch('flash', message: 'Ajouté au panier ✓');
    }

    public function render(CurrentRelais $relais)
    {
        $relaisId = $relais->id();

        $produits = Product::query()
            ->where('actif', true)
            ->whereHas('inventories', fn ($q) => $q->where('relais_id', $relaisId)->where('actif', true))
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->when($this->search !== '', fn ($q) => $q->where('nom', 'like', '%' . $this->search . '%'))
            ->with(['inventories' => fn ($q) => $q->where('relais_id', $relaisId), 'media', 'category'])
            ->orderBy('nom')
            ->get();

        $categories = Category::where('actif', true)->orderBy('position')->get();

        return view('livewire.storefront.catalog', [
            'produits' => $produits,
            'categories' => $categories,
            'relaisId' => $relaisId,
        ]);
    }
}
