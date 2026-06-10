<?php

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\Concern;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Testimonial;
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

    #[Url(as: 'besoin')]
    public ?string $besoin = null;

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
        $this->reset(['besoin', 'search']);
        $this->categoryId = $categoryId;
    }

    public function ouvrirBesoin(string $slug): void
    {
        $this->reset(['categoryId', 'search']);
        $this->besoin = $slug;
    }

    public function retour(): void
    {
        $this->reset(['categoryId', 'besoin', 'search']);
    }

    public function render(CurrentRelais $relais)
    {
        $relaisId = $relais->id();
        $dispoSurRelais = fn ($q) => $q->where('relais_id', $relaisId)->where('actif', true);

        $modeProduits = $this->categoryId !== null || $this->besoin !== null || $this->search !== '';

        $produits = collect();
        $categories = collect();
        $currentCategory = null;
        $currentConcern = null;
        $heroSlides = collect();
        $enAvant = collect();
        $concerns = collect();
        $avis = collect();

        if ($modeProduits) {
            $currentConcern = $this->besoin ? Concern::where('slug', $this->besoin)->first() : null;
            $currentCategory = $this->categoryId ? Category::find($this->categoryId) : null;

            $produits = Product::query()
                ->where('actif', true)
                ->whereHas('inventories', $dispoSurRelais)
                ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
                ->when($currentConcern, fn ($q) => $q->whereHas('concerns', fn ($c) => $c->where('concerns.id', $currentConcern->id)))
                ->when($this->search !== '', fn ($q) => $q->where('nom', 'like', '%' . $this->search . '%'))
                ->with(['inventories' => fn ($q) => $q->where('relais_id', $relaisId), 'media', 'category'])
                ->orderBy('nom')
                ->get();
        } else {
            $heroSlides = HeroSlide::where('actif', true)->with('media')->orderBy('position')->get();

            $categories = Category::query()
                ->where('actif', true)
                ->whereHas('products', fn ($q) => $q->where('actif', true)->whereHas('inventories', $dispoSurRelais))
                ->withCount(['products as dispo_count' => fn ($q) => $q->where('actif', true)->whereHas('inventories', $dispoSurRelais)])
                ->with('media')
                ->orderBy('position')
                ->get();

            // "Notre sélection du moment" : produits choisis en admin (en_avant).
            // À défaut de sélection, on retombe sur les plus récents.
            $base = Product::query()
                ->where('actif', true)
                ->whereHas('inventories', $dispoSurRelais)
                ->with(['inventories' => fn ($q) => $q->where('relais_id', $relaisId), 'media', 'category']);

            $enAvant = (clone $base)->where('en_avant', true)->orderBy('nom')->take(12)->get();
            if ($enAvant->isEmpty()) {
                $enAvant = $base->latest()->take(8)->get();
            }

            $concerns = Concern::where('actif', true)
                ->whereHas('products', fn ($q) => $q->where('actif', true)->whereHas('inventories', $dispoSurRelais))
                ->orderBy('position')
                ->get();

            $avis = Testimonial::where('actif', true)->orderBy('position')->get();
        }

        return view('livewire.storefront.catalog', [
            'modeProduits' => $modeProduits,
            'produits' => $produits,
            'categories' => $categories,
            'currentCategory' => $currentCategory,
            'currentConcern' => $currentConcern,
            'heroSlides' => $heroSlides,
            'enAvant' => $enAvant,
            'concerns' => $concerns,
            'avis' => $avis,
            'relaisId' => $relaisId,
        ]);
    }
}
