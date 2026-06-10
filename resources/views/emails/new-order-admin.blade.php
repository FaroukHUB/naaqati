<!DOCTYPE html>
<html lang="fr">
<body style="font-family: Arial, sans-serif; color:#333; background:#fafaf9; padding:20px;">
    <div style="max-width:560px;margin:auto;background:#fff;border-radius:12px;padding:24px;">
        <h2 style="color:#B76E79;margin:0 0 4px;">🛍️ Nouvelle commande</h2>
        <p style="margin:0 0 16px;color:#777;">N° {{ $order->numero }}</p>

        <p><strong>Cliente :</strong> {{ $order->customer?->nom }} ({{ $order->customer?->telephone }})</p>
        <p><strong>Retrait :</strong>
            {{ $order->date_retrait ? \Carbon\Carbon::parse($order->date_retrait)->isoFormat('dddd D MMMM') : 'À définir' }}
            @if ($order->creneau_retrait) · {{ str_replace('_', ' ', $order->creneau_retrait) }} @endif
        </p>
        <p><strong>Qui récupère :</strong> {{ $order->recuperateur ?? '—' }}</p>

        <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">

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

        @if ($order->commentaire)
            <p style="color:#777;"><em>Commentaire : {{ $order->commentaire }}</em></p>
        @endif
    </div>
</body>
</html>
