<?php

namespace App\Services;

use App\Models\MessageTemplate;
use App\Models\Order;
use App\Models\WhatsappMessage;

/**
 * Service WhatsApp.
 *
 * V1 (o2switch) : génère un lien wa.me pré-rempli que l'admin clique
 *                 pour envoyer manuellement. Aucune API requise.
 * V2 (VPS)      : implémenter envoyerViaApi() avec WhatsApp Cloud API
 *                 (templates approuvés + webhooks de statut).
 */
class WhatsappService
{
    /** Construit un lien wa.me?text=... pré-rempli. */
    public function lienWaMe(string $telephone, string $message): string
    {
        $numero = $this->normaliserNumero($telephone);

        return 'https://wa.me/' . $numero . '?text=' . rawurlencode($message);
    }

    /**
     * Prépare le message "commande prête" : rend le template, enregistre la trace
     * en base (statut en_attente) et renvoie le lien wa.me à cliquer.
     */
    public function preparerCommandePrete(Order $order): array
    {
        $template = MessageTemplate::where('cle', 'commande_prete')->where('actif', true)->first();
        $relais = $order->relais;
        $customer = $order->customer;

        $variables = [
            'nom' => $customer->nom,
            'numero' => $order->numero,
            'relais' => $relais?->nom,
            'adresse' => $relais?->adresse,
            'batiment' => $relais?->batiment,
            'code_portail' => $relais?->code_portail,
            'etage' => $relais?->etage,
            'date' => $order->date_retrait?->format('d/m/Y'),
            'creneau' => $order->creneau_retrait,
        ];

        $corps = $template
            ? $template->render($variables)
            : "Bonjour {$customer->nom}, votre commande {$order->numero} est prête.";

        $message = WhatsappMessage::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'template_id' => $template?->id,
            'corps_rendu' => $corps,
            'canal' => 'wa_link',
            'statut' => 'en_attente',
        ]);

        return [
            'message' => $message,
            'lien' => $this->lienWaMe($customer->telephone, $corps),
        ];
    }

    /** Marque un message comme envoyé (clic admin sur le lien wa.me). */
    public function marquerEnvoye(WhatsappMessage $message): void
    {
        $message->update(['statut' => 'envoye', 'sent_at' => now()]);
    }

    /**
     * Normalise un numéro algérien au format international sans "+".
     * Ex: "0555 12 34 56" -> "213555123456".
     */
    private function normaliserNumero(string $telephone): string
    {
        $num = preg_replace('/\D+/', '', $telephone);

        if (str_starts_with($num, '00')) {
            $num = substr($num, 2);
        }

        // Numéro local algérien commençant par 0 -> préfixe pays 213.
        if (str_starts_with($num, '0')) {
            $num = '213' . substr($num, 1);
        }

        return $num;
    }
}
