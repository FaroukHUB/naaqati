<?php

namespace App\Services;

use App\Models\Packaging;
use App\Models\Product;
use App\Support\CurrentRelais;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Panier multi-paquets (stocké en session).
 *
 * Structure session 'cart' :
 *   [ 'packages' => [
 *        [ 'key' => 'abc', 'packaging_id' => ?int, 'destinataire' => ?string,
 *          'message' => ?string, 'items' => [product_id => quantite] ],
 *        ...
 *   ]]
 *
 * Il y a toujours au moins un paquet. Montants en centimes.
 */
class CartService
{
    private const KEY = 'cart';

    public function __construct(private readonly CurrentRelais $relais) {}

    // ---------------------------------------------------------------- Lecture

    /** Nombre total d'articles (toutes paquets confondus). */
    public function count(): int
    {
        return collect($this->raw()['packages'])
            ->sum(fn ($p) => array_sum($p['items']));
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Paquets enrichis pour l'affichage.
     *
     * @return Collection<int, array>
     */
    public function packages(): Collection
    {
        $cart = $this->raw();

        // Tous les produits référencés, chargés en une requête.
        $ids = collect($cart['packages'])->flatMap(fn ($p) => array_keys($p['items']))->unique()->all();
        $produits = empty($ids)
            ? collect()
            : Product::whereIn('id', $ids)->with('inventories', 'media')->get()->keyBy('id');

        $relaisId = $this->relais->id();
        $packagings = Packaging::whereIn('id', collect($cart['packages'])->pluck('packaging_id')->filter()->all())
            ->get()->keyBy('id');

        return collect($cart['packages'])->values()->map(function ($p, $i) use ($produits, $packagings, $relaisId) {
            $lines = collect($p['items'])->map(function ($qte, $pid) use ($produits, $relaisId) {
                $produit = $produits->get($pid);
                if (! $produit) {
                    return null;
                }
                $prix = $produit->prixPourRelais($relaisId);

                return [
                    'product' => $produit,
                    'quantite' => (int) $qte,
                    'prix_unitaire' => $prix,
                    'total_ligne' => $prix * (int) $qte,
                ];
            })->filter()->values();

            $packaging = $p['packaging_id'] ? $packagings->get($p['packaging_id']) : null;
            $sousTotal = (int) $lines->sum('total_ligne');
            $frais = $lines->isNotEmpty() ? (int) ($packaging?->prix ?? 0) : 0;

            return [
                'key' => $p['key'],
                'index' => $i + 1,
                'packaging' => $packaging,
                'packaging_id' => $p['packaging_id'],
                'destinataire' => $p['destinataire'] ?? null,
                'message' => $p['message'] ?? null,
                'lines' => $lines,
                'sous_total' => $sousTotal,
                'frais' => $frais,
                'total' => $sousTotal + $frais,
            ];
        });
    }

    public function sousTotal(): int
    {
        return (int) $this->packages()->sum('sous_total');
    }

    public function fraisTotal(): int
    {
        return (int) $this->packages()->sum('frais');
    }

    public function total(): int
    {
        return (int) $this->packages()->sum('total');
    }

    /** Quantités cumulées par produit (pour la réservation de stock). */
    public function quantitesParProduit(): array
    {
        $totaux = [];
        foreach ($this->raw()['packages'] as $p) {
            foreach ($p['items'] as $pid => $qte) {
                $totaux[$pid] = ($totaux[$pid] ?? 0) + (int) $qte;
            }
        }

        return $totaux;
    }

    // -------------------------------------------------------------- Mutations

    /** Ajoute un produit (au premier paquet par défaut, ou au paquet ciblé). */
    public function add(int $productId, int $quantite = 1, ?string $packageKey = null): void
    {
        $cart = $this->raw();
        $idx = $this->packageIndex($cart, $packageKey) ?? 0;

        $current = $cart['packages'][$idx]['items'][$productId] ?? 0;
        $new = $current + $quantite;

        if ($new < 1) {
            unset($cart['packages'][$idx]['items'][$productId]);
        } else {
            $cart['packages'][$idx]['items'][$productId] = $new;
        }

        $this->save($cart);
    }

    public function setQuantite(string $packageKey, int $productId, int $quantite): void
    {
        $cart = $this->raw();
        $idx = $this->packageIndex($cart, $packageKey);
        if ($idx === null) {
            return;
        }

        if ($quantite < 1) {
            unset($cart['packages'][$idx]['items'][$productId]);
        } else {
            $cart['packages'][$idx]['items'][$productId] = $quantite;
        }

        $this->save($cart);
    }

    public function removeItem(string $packageKey, int $productId): void
    {
        $cart = $this->raw();
        $idx = $this->packageIndex($cart, $packageKey);
        if ($idx !== null) {
            unset($cart['packages'][$idx]['items'][$productId]);
            $this->save($cart);
        }
    }

    /** Déplace tout le stock d'un produit d'un paquet vers un autre. */
    public function moveItem(int $productId, string $fromKey, string $toKey): void
    {
        if ($fromKey === $toKey) {
            return;
        }

        $cart = $this->raw();
        $from = $this->packageIndex($cart, $fromKey);
        $to = $this->packageIndex($cart, $toKey);
        if ($from === null || $to === null) {
            return;
        }

        $qte = $cart['packages'][$from]['items'][$productId] ?? 0;
        if ($qte < 1) {
            return;
        }

        unset($cart['packages'][$from]['items'][$productId]);
        $cart['packages'][$to]['items'][$productId] = ($cart['packages'][$to]['items'][$productId] ?? 0) + $qte;

        $this->save($cart);
    }

    public function addPackage(): string
    {
        $cart = $this->raw();
        $package = $this->newPackage();
        $cart['packages'][] = $package;
        $this->save($cart);

        return $package['key'];
    }

    /** Supprime un paquet (les articles éventuels sont déplacés vers le premier paquet). */
    public function removePackage(string $packageKey): void
    {
        $cart = $this->raw();
        if (count($cart['packages']) <= 1) {
            return;
        }

        $idx = $this->packageIndex($cart, $packageKey);
        if ($idx === null) {
            return;
        }

        $items = $cart['packages'][$idx]['items'];
        array_splice($cart['packages'], $idx, 1);

        // Réaffecter les articles au premier paquet.
        foreach ($items as $pid => $qte) {
            $cart['packages'][0]['items'][$pid] = ($cart['packages'][0]['items'][$pid] ?? 0) + $qte;
        }

        $this->save($cart);
    }

    public function setPackaging(string $packageKey, ?int $packagingId): void
    {
        $cart = $this->raw();
        $idx = $this->packageIndex($cart, $packageKey);
        if ($idx !== null) {
            $cart['packages'][$idx]['packaging_id'] = $packagingId;
            $this->save($cart);
        }
    }

    public function setInfos(string $packageKey, ?string $destinataire, ?string $message): void
    {
        $cart = $this->raw();
        $idx = $this->packageIndex($cart, $packageKey);
        if ($idx !== null) {
            $cart['packages'][$idx]['destinataire'] = $destinataire ?: null;
            $cart['packages'][$idx]['message'] = $message ?: null;
            $this->save($cart);
        }
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    // ---------------------------------------------------------------- Interne

    private function newPackage(): array
    {
        return [
            'key' => Str::random(10),
            'packaging_id' => null,
            'destinataire' => null,
            'message' => null,
            'items' => [],
        ];
    }

    private function packageIndex(array $cart, ?string $key): ?int
    {
        if ($key === null) {
            return 0;
        }
        foreach ($cart['packages'] as $i => $p) {
            if ($p['key'] === $key) {
                return $i;
            }
        }

        return null;
    }

    private function raw(): array
    {
        $cart = session(self::KEY);

        if (! is_array($cart) || empty($cart['packages'])) {
            return ['packages' => [$this->newPackage()]];
        }

        return $cart;
    }

    private function save(array $cart): void
    {
        // Réindexe proprement.
        $cart['packages'] = array_values($cart['packages']);
        session()->put(self::KEY, $cart);
    }
}
