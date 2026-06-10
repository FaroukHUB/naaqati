<!DOCTYPE html>
<html lang="fr">
<body style="font-family: Arial, sans-serif; color:#333; background:#fafaf9; padding:20px;">
    <div style="max-width:560px;margin:auto;background:#fff;border-radius:12px;padding:24px;">
        <h2 style="color:#A1763C;margin:0 0 8px;">Merci {{ $order->customer?->nom }} ! 🌸</h2>
        <p style="margin:0 0 16px;color:#555;">Votre commande <strong>{{ $order->numero }}</strong> est bien enregistrée.</p>

        <div style="background:#fbf1f3;border-radius:8px;padding:16px;margin-bottom:16px;">
            <p style="margin:0 0 6px;"><strong>Retrait :</strong>
                {{ $order->date_retrait ? \Carbon\Carbon::parse($order->date_retrait)->isoFormat('dddd D MMMM') : 'À définir' }}
                @if ($order->creneau_retrait) · {{ str_replace('_', ' ', $order->creneau_retrait) }} @endif
            </p>
            <p style="margin:0;"><strong>Point relais :</strong> {{ $order->relais?->nom }}</p>
        </div>

        @foreach ($order->packages as $pkg)
            <p style="margin:0 0 4px;"><strong>🎁 {{ $pkg->packaging?->nom ?? 'Sachet kraft' }}</strong>
                @if ($pkg->nom_destinataire) — pour {{ $pkg->nom_destinataire }} @endif
            </p>
            <ul style="margin:0 0 12px;color:#555;">
                @foreach ($pkg->items as $it)
                    <li>{{ $it->quantite }}× {{ $it->nom_snapshot }}</li>
                @endforeach
            </ul>
        @endforeach

        <p style="font-size:18px;"><strong>Total : {{ number_format($order->total / 100, 0, ',', ' ') }} DA</strong></p>
        <p style="color:#777;font-size:14px;">Vous recevrez un message WhatsApp dès que votre commande sera prête à récupérer. Paiement sur place.</p>
    </div>
</body>
</html>
