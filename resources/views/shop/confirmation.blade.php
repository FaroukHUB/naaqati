<x-shop-layout :title="'Commande confirmée — Naaqati'">
    <div class="mx-auto max-w-xl">
        <div class="rounded-2xl border border-stone-100 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full" style="background-color:#F4E4E7;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="#B76E79" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-stone-900">Merci {{ $order->customer->nom }} !</h1>
            <p class="mt-1 text-stone-500">Votre commande est bien enregistrée.</p>

            <div class="mt-6 space-y-3 rounded-xl bg-stone-50 p-5 text-left text-sm">
                <div class="flex justify-between"><span class="text-stone-500">N° de commande</span><span class="font-semibold text-stone-800">{{ $order->numero }}</span></div>
                <div class="flex justify-between"><span class="text-stone-500">Retrait</span><span class="font-semibold text-stone-800">{{ \Carbon\Carbon::parse($order->date_retrait)->isoFormat('dddd D MMMM') }} · {{ ucfirst(str_replace('_',' ',$order->creneau_retrait)) }}</span></div>
                <div class="flex justify-between"><span class="text-stone-500">Point relais</span><span class="font-semibold text-stone-800">{{ $order->relais->nom }}</span></div>
                <div class="flex justify-between border-t border-stone-200 pt-3 text-base"><span class="font-bold text-stone-900">Total</span><span class="font-bold" style="color:#B76E79;">{{ number_format($order->total / 100, 0, ',', ' ') }} DA</span></div>
            </div>

            {{-- Détail des paquets --}}
            @if ($order->packages->isNotEmpty())
                <div class="mt-6 space-y-3 text-left">
                    @foreach ($order->packages as $pkg)
                        <div class="rounded-xl border border-stone-100 p-3">
                            <p class="text-sm font-semibold text-stone-800">
                                🎁 {{ $pkg->packaging?->nom ?? 'Sachet kraft' }}
                                @if ($pkg->nom_destinataire) <span class="font-normal text-stone-400">· pour {{ $pkg->nom_destinataire }}</span> @endif
                            </p>
                            <ul class="mt-1 text-xs text-stone-500">
                                @foreach ($pkg->items as $it)
                                    <li>{{ $it->quantite }}× {{ $it->nom_snapshot }}</li>
                                @endforeach
                            </ul>
                            @if ($pkg->message_cadeau)
                                <p class="mt-1 text-xs italic text-stone-400">« {{ $pkg->message_cadeau }} »</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <p class="mt-6 text-sm text-stone-500">
                Nous préparons votre commande. Vous recevrez un message <strong>WhatsApp</strong> dès qu'elle sera prête à récupérer, avec l'adresse et le code d'accès.
            </p>

            <a href="{{ route('shop.catalog') }}" class="mt-6 inline-block rounded-xl px-6 py-3 text-sm font-semibold text-white" style="background-color:#B76E79;">
                Retour à la boutique
            </a>
        </div>
    </div>
</x-shop-layout>
