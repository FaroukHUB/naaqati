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

    public function ouvrirCategorie(int $categoryId): void
    {
        $this->categoryId = $categoryId;
        $this->search = '';
    }

    public function retour(): void
    {
        $this->categoryId = null;
        $this->search = '';
    }

    public function render(CurrentRelais $relais)
    {
        $relaisId = $relais->id();

        // Contrainte « produit disponible sur ce relais ».
        $dispoSurRelais = fn ($q) => $q->where('relais_id', $relaisId)->where('actif', true);

        $modeProduits = $this->categoryId !== null || $this->search !== '';

        $produits = collect();
        $categories = collect();
        $currentCategory = null;

        if ($modeProduits) {
            $produits = Product::query()
                ->where('actif', true)
                ->whereHas('inventories', $dispoSurRelais)
                ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
                ->when($this->search !== '', fn ($q) => $q->where('nom', 'like', '%' . $this->search . '%'))
                ->with(['inventories' => fn ($q) => $q->where('relais_id', $relaisId), 'media', 'category'])
                ->orderBy('nom')
                ->get();

            $currentCategory = $this->categoryId ? Category::find($this->categoryId) : null;
        } else {
            // Grille des catégories ayant au moins un produit disponible.
            $categories = Category::query()
                ->where('actif', true)
                ->whereHas('products', fn ($q) => $q->where('actif', true)->whereHas('inventories', $dispoSurRelais))
                ->withCount(['products as dispo_count' => fn ($q) => $q->where('actif', true)->whereHas('inventories', $dispoSurRelais)])
                ->with('media')
                ->orderBy('position')
                ->get();
        }

        return view('livewire.storefront.catalog', [
            'modeProduits' => $modeProduits,
            'produits' => $produits,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'relaisId' => $relaisId,
        ]);
    }
}
