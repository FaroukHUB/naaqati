<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Orchestration des commandes : changement de statut + impact stock + historique.
 *
 * Machine à états :
 *   recue -> en_preparation -> prete -> recuperee -> terminee
 *   (annulee possible tant que non récupérée)
 */
class OrderService
{
    public function __construct(private readonly StockService $stock) {}

    public function changerStatut(Order $order, OrderStatus $nouveau, ?string $note = null): Order
    {
        $ancien = $order->statut;

        if ($ancien === $nouveau) {
            return $order;
        }

        if (! $ancien->peutAllerVers($nouveau)) {
            throw new InvalidArgumentException(
                "Transition interdite : {$ancien->value} -> {$nouveau->value}."
            );
        }

        return DB::transaction(function () use ($order, $ancien, $nouveau, $note) {
            $quantites = $this->quantitesParProduit($order);

            // Impact stock selon la transition.
            if ($nouveau === OrderStatus::Recuperee) {
                $this->stock->vendrePourCommande($order, $quantites);
            } elseif ($nouveau === OrderStatus::Annulee) {
                $this->stock->libererPourCommande($order, $quantites);
            }

            $order->update(['statut' => $nouveau]);

            $order->statusHistories()->create([
                'ancien_statut' => $ancien,
                'nouveau_statut' => $nouveau,
                'user_id' => Auth::id(),
                'note' => $note,
            ]);

            return $order->refresh();
        });
    }

    /** [product_id => quantite] sur les lignes produits (hors coffrets, gérés à part). */
    private function quantitesParProduit(Order $order): array
    {
        return $order->items()
            ->whereNotNull('product_id')
            ->get()
            ->groupBy('product_id')
            ->map(fn ($items) => $items->sum('quantite'))
            ->all();
    }
}
