<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsappService;
use Illuminate\Http\RedirectResponse;

/**
 * Génère le message WhatsApp « commande prête », le trace en base,
 * et redirige vers le lien wa.me pré-rempli (ouvert dans un nouvel onglet).
 */
class OrderWhatsappController extends Controller
{
    public function __invoke(Order $order, WhatsappService $whatsapp): RedirectResponse
    {
        $resultat = $whatsapp->preparerCommandePrete($order);
        $whatsapp->marquerEnvoye($resultat['message']);

        return redirect()->away($resultat['lien']);
    }
}
