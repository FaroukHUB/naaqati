<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Support\CurrentRelais;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Transforme le panier multi-paquets en commande réelle :
 * cliente, commande, paquets, lignes (snapshots), réservation du stock.
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

        $packages = $this->cart->packages()->filter(fn ($p) => $p['lines']->isNotEmpty())->values();
        $relaisId = $this->relais->id();
        $sousTotal = $this->cart->sousTotal();
        $fraisTotal = $this->cart->fraisTotal();
        $quantites = $this->cart->quantitesParProduit();

        return DB::transaction(function () use ($infos, $packages, $relaisId, $sousTotal, $fraisTotal, $quantites) {
            // 1. Cliente.
            $customer = Customer::firstOrCreate(
                ['telephone' => $infos['telephone']],
                ['nom' => $infos['nom'], 'email' => $infos['email'] ?? null],
            );
            $customer->fill([
                'nom' => $infos['nom'],
                'email' => $infos['email'] ?? $customer->email,
            ])->save();

            // 2. Commande.
            $order = Order::create([
                'customer_id' => $customer->id,
                'relais_id' => $relaisId,
                'devise_code' => $this->relais->devise(),
                'sous_total' => $sousTotal,
                'packaging_id' => null,
                'frais_emballage' => $fraisTotal,
                'total' => $sousTotal + $fraisTotal,
                'date_retrait' => $infos['date_retrait'] ?? null,
                'creneau_retrait' => $infos['creneau'] ?? null,
                'recuperateur' => $infos['recuperateur'] ?? null,
                'commentaire' => $infos['commentaire'] ?? null,
            ]);

            // 3. Paquets + lignes (snapshots).
            foreach ($packages as $i => $pkg) {
                $orderPackage = $order->packages()->create([
                    'packaging_id' => $pkg['packaging_id'],
                    'nom_destinataire' => $pkg['destinataire'],
                    'message_cadeau' => $pkg['message'],
                    'frais_emballage' => $pkg['frais'],
                    'position' => $i + 1,
                ]);

                foreach ($pkg['lines'] as $line) {
                    $order->items()->create([
                        'order_package_id' => $orderPackage->id,
                        'product_id' => $line['product']->id,
                        'nom_snapshot' => $line['product']->nom,
                        'prix_unitaire' => $line['prix_unitaire'],
                        'quantite' => $line['quantite'],
                        'total_ligne' => $line['total_ligne'],
                    ]);
                }
            }

            // 4. Réservation du stock (quantités cumulées par produit).
            $this->stock->reserverPourCommande($order, $quantites);

            // 5. Historique.
            $order->statusHistories()->create([
                'ancien_statut' => null,
                'nouveau_statut' => $order->statut,
                'note' => 'Commande passée depuis la boutique',
            ]);

            // 6. CRM.
            $customer->increment('nb_commandes');
            $customer->increment('total_depense', $order->total);
            $customer->update(['derniere_commande_at' => now()]);

            // 7. Vider le panier.
            $this->cart->clear();

            return $order;
        });
    }
}
