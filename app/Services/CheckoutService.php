<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Support\CurrentRelais;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Transforme le panier (session) en commande réelle :
 * cliente, commande, lignes (snapshots), réservation du stock.
 */
class CheckoutService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly StockService $stock,
        private readonly CurrentRelais $relais,
    ) {}

    /**
     * @param  array{nom:string, telephone:string, email?:?string, date_retrait:string, creneau:string, commentaire?:?string}  $infos
     */
    public function passerCommande(array $infos): Order
    {
        if ($this->cart->isEmpty()) {
            throw new RuntimeException('Votre panier est vide.');
        }

        $lines = $this->cart->lines();
        $relaisId = $this->relais->id();

        return DB::transaction(function () use ($infos, $lines, $relaisId) {
            // 1. Cliente (retrouvée par téléphone, sinon créée).
            $customer = Customer::firstOrCreate(
                ['telephone' => $infos['telephone']],
                ['nom' => $infos['nom'], 'email' => $infos['email'] ?? null],
            );

            // Mise à jour du nom/email si la cliente revient.
            $customer->fill([
                'nom' => $infos['nom'],
                'email' => $infos['email'] ?? $customer->email,
            ])->save();

            // 2. Commande.
            $sousTotal = $this->cart->sousTotal();
            $frais = $this->cart->fraisEmballage();

            $order = Order::create([
                'customer_id' => $customer->id,
                'relais_id' => $relaisId,
                'devise_code' => $this->relais->devise(),
                'sous_total' => $sousTotal,
                'packaging_id' => $this->cart->packaging()?->id,
                'frais_emballage' => $frais,
                'total' => $sousTotal + $frais,
                'date_retrait' => $infos['date_retrait'],
                'creneau_retrait' => $infos['creneau'],
                'commentaire' => $infos['commentaire'] ?? null,
            ]);

            // 3. Lignes (snapshots prix/nom figés).
            $quantites = [];
            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'nom_snapshot' => $line['product']->nom,
                    'prix_unitaire' => $line['prix_unitaire'],
                    'quantite' => $line['quantite'],
                    'total_ligne' => $line['total_ligne'],
                ]);
                $quantites[$line['product']->id] = $line['quantite'];
            }

            // 4. Réservation du stock (verrou anti-survente).
            $this->stock->reserverPourCommande($order, $quantites);

            // 5. Historique : création.
            $order->statusHistories()->create([
                'ancien_statut' => null,
                'nouveau_statut' => $order->statut,
                'note' => 'Commande passée depuis la boutique',
            ]);

            // 6. Agrégats cliente (CRM).
            $customer->increment('nb_commandes');
            $customer->increment('total_depense', $order->total);
            $customer->update(['derniere_commande_at' => now()]);

            // 7. Vider le panier.
            $this->cart->clear();

            return $order;
        });
    }
}
