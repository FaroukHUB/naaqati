<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Gère le stock par point relais avec sécurité transactionnelle.
 *
 * Cycle de vie :
 *   commande reçue    -> reserve()  : disponible--, reserve++
 *   commande récupérée -> vendre()   : reserve--, vendu++
 *   commande annulée  -> liberer()  : reserve--, disponible++
 *
 * Chaque opération écrit une ligne stock_movements (audit complet).
 */
class StockService
{
    /**
     * Réserve le stock pour une commande validée.
     * Verrou pessimiste (lockForUpdate) pour éviter la survente concurrente.
     *
     * @param  array<int,int>  $quantitesParProduit  [product_id => quantite]
     */
    public function reserverPourCommande(Order $order, array $quantitesParProduit): void
    {
        DB::transaction(function () use ($order, $quantitesParProduit) {
            foreach ($quantitesParProduit as $productId => $quantite) {
                $inventory = Inventory::where('product_id', $productId)
                    ->where('relais_id', $order->relais_id)
                    ->lockForUpdate()
                    ->first();

                if (! $inventory) {
                    throw new RuntimeException("Aucun stock pour le produit {$productId} sur ce relais.");
                }

                if ($inventory->stock_disponible < $quantite) {
                    throw new RuntimeException(
                        "Stock insuffisant pour le produit {$productId} (dispo: {$inventory->stock_disponible}, demandé: {$quantite})."
                    );
                }

                $inventory->decrement('stock_disponible', $quantite);
                $inventory->increment('stock_reserve', $quantite);

                $this->logMouvement($inventory, StockMovementType::Reservation, -$quantite, $order, 'Réservation commande');
            }
        });
    }

    /** Confirme la vente (retrait effectif) : reserve -> vendu. */
    public function vendrePourCommande(Order $order, array $quantitesParProduit): void
    {
        DB::transaction(function () use ($order, $quantitesParProduit) {
            foreach ($quantitesParProduit as $productId => $quantite) {
                $inventory = $this->lockInventory($productId, $order->relais_id);

                $inventory->decrement('stock_reserve', $quantite);
                $inventory->increment('stock_vendu', $quantite);

                $this->logMouvement($inventory, StockMovementType::Vente, $quantite, $order, 'Vente (retrait)');
            }
        });
    }

    /** Annulation : remet le stock réservé en disponible. */
    public function libererPourCommande(Order $order, array $quantitesParProduit): void
    {
        DB::transaction(function () use ($order, $quantitesParProduit) {
            foreach ($quantitesParProduit as $productId => $quantite) {
                $inventory = $this->lockInventory($productId, $order->relais_id);

                $inventory->decrement('stock_reserve', $quantite);
                $inventory->increment('stock_disponible', $quantite);

                $this->logMouvement($inventory, StockMovementType::Liberation, $quantite, $order, 'Libération (annulation)');
            }
        });
    }

    /** Ajustement manuel du stock disponible par un admin. */
    public function ajusterManuellement(Inventory $inventory, int $nouveauDisponible, ?string $motif = null): void
    {
        DB::transaction(function () use ($inventory, $nouveauDisponible, $motif) {
            $inventory = Inventory::whereKey($inventory->getKey())->lockForUpdate()->first();
            $delta = $nouveauDisponible - $inventory->stock_disponible;

            $inventory->update(['stock_disponible' => $nouveauDisponible]);

            $this->logMouvement(
                $inventory,
                StockMovementType::Ajustement,
                $delta,
                null,
                $motif ?? 'Ajustement manuel'
            );
        });
    }

    private function lockInventory(int $productId, int $relaisId): Inventory
    {
        $inventory = Inventory::where('product_id', $productId)
            ->where('relais_id', $relaisId)
            ->lockForUpdate()
            ->first();

        if (! $inventory) {
            throw new RuntimeException("Inventaire introuvable (produit {$productId}, relais {$relaisId}).");
        }

        return $inventory;
    }

    private function logMouvement(
        Inventory $inventory,
        StockMovementType $type,
        int $quantite,
        ?Order $order,
        string $motif
    ): void {
        $inventory->movements()->create([
            'relais_id' => $inventory->relais_id,
            'type' => $type,
            'quantite' => $quantite,
            'motif' => $motif,
            'order_id' => $order?->id,
            'user_id' => Auth::id(),
        ]);
    }
}
