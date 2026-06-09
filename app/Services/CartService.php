<?php

namespace App\Services;

use App\Models\Packaging;
use App\Models\Product;
use App\Support\CurrentRelais;
use Illuminate\Support\Collection;

/**
 * Panier stocké en session (pas besoin de base pour un panier invité).
 *
 * Structure session 'cart' :
 *   [ 'items' => [product_id => quantite], 'packaging_id' => ?int ]
 *
 * Tous les montants manipulés sont en centimes.
 */
class CartService
{
    private const KEY = 'cart';

    public function __construct(private readonly CurrentRelais $relais) {}

    /** Ajoute (ou incrémente) un produit au panier. */
    public function add(int $productId, int $quantite = 1): void
    {
        $cart = $this->raw();
        $cart['items'][$productId] = ($cart['items'][$productId] ?? 0) + $quantite;

        if ($cart['items'][$productId] < 1) {
            unset($cart['items'][$productId]);
        }

        $this->save($cart);
    }

    public function setQuantite(int $productId, int $quantite): void
    {
        $cart = $this->raw();

        if ($quantite < 1) {
            unset($cart['items'][$productId]);
        } else {
            $cart['items'][$productId] = $quantite;
        }

        $this->save($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart['items'][$productId]);
        $this->save($cart);
    }

    public function setPackaging(?int $packagingId): void
    {
        $cart = $this->raw();
        $cart['packaging_id'] = $packagingId;
        $this->save($cart);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    /** Nombre total d'articles (somme des quantités). */
    public function count(): int
    {
        return collect($this->raw()['items'])->sum();
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Lignes détaillées du panier.
     *
     * @return Collection<int, array{product: Product, quantite: int, prix_unitaire: int, total_ligne: int}>
     */
    public function lines(): Collection
    {
        $items = $this->raw()['items'];

        if (empty($items)) {
            return collect();
        }

        $relaisId = $this->relais->id();

        return Product::whereIn('id', array_keys($items))
            ->with('inventories')
            ->get()
            ->map(function (Product $product) use ($items, $relaisId) {
                $prix = $product->prixPourRelais($relaisId);
                $qte = (int) $items[$product->id];

                return [
                    'product' => $product,
                    'quantite' => $qte,
                    'prix_unitaire' => $prix,
                    'total_ligne' => $prix * $qte,
                ];
            })
            ->values();
    }

    public function packaging(): ?Packaging
    {
        $id = $this->raw()['packaging_id'] ?? null;

        return $id ? Packaging::find($id) : null;
    }

    public function fraisEmballage(): int
    {
        return (int) ($this->packaging()?->prix ?? 0);
    }

    /** Sous-total produits (centimes). */
    public function sousTotal(): int
    {
        return (int) $this->lines()->sum('total_ligne');
    }

    /** Total panier = produits + emballage (centimes). */
    public function total(): int
    {
        return $this->sousTotal() + $this->fraisEmballage();
    }

    private function raw(): array
    {
        return session(self::KEY, ['items' => [], 'packaging_id' => null]);
    }

    private function save(array $cart): void
    {
        session()->put(self::KEY, $cart);
    }
}
