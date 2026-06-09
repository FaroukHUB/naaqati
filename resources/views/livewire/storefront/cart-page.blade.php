<div>
    <h1 class="mb-5 text-2xl font-bold text-stone-900">Mon panier</h1>

    @if ($lines->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-200 bg-white p-12 text-center">
            <p class="text-stone-400">Votre panier est vide.</p>
            <a href="{{ route('shop.catalog') }}" class="mt-4 inline-block rounded-xl px-5 py-2.5 text-sm font-semibold text-white" style="background-color:#B76E79;">
                Voir les produits
            </a>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Lignes --}}
            <div class="space-y-3 lg:col-span-2">
                @foreach ($lines as $line)
                    @php $produit = $line['product']; $image = $produit->getFirstMediaUrl('images'); @endphp
                    <div class="flex items-center gap-4 rounded-2xl border border-stone-100 bg-white p-3 shadow-sm">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-stone-100">
                            @if ($image)
                                <img src="{{ $image }}" class="h-full w-full object-cover" alt="">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-sm font-semibold text-stone-800">{{ $produit->nom }}</h3>
                            <p class="text-sm text-stone-500">{{ number_format($line['prix_unitaire'] / 100, 0, ',', ' ') }} DA</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="changerQuantite({{ $produit->id }}, -1)" class="h-8 w-8 rounded-full border border-stone-200 text-stone-600 hover:bg-stone-50">−</button>
                            <span class="w-6 text-center text-sm font-semibold">{{ $line['quantite'] }}</span>
                            <button wire:click="changerQuantite({{ $produit->id }}, 1)" class="h-8 w-8 rounded-full border border-stone-200 text-stone-600 hover:bg-stone-50">+</button>
                        </div>
                        <div class="w-20 text-right text-sm font-bold text-stone-800">
                            {{ number_format($line['total_ligne'] / 100, 0, ',', ' ') }} DA
                        </div>
                        <button wire:click="supprimer({{ $produit->id }})" class="text-stone-300 hover:text-red-400" title="Retirer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endforeach

                {{-- Emballage --}}
                <div class="rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                    <label class="mb-3 block text-sm font-semibold text-stone-700">Emballage</label>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($emballages as $emb)
                            @php $embImg = $emb->getFirstMediaUrl('image'); @endphp
                            <button type="button" wire:click="choisirEmballage({{ $emb->id }})"
                                    @class([
                                        'group relative overflow-hidden rounded-xl border-2 p-3 text-left transition',
                                        'bg-white border-stone-200 hover:border-stone-300' => $packagingId !== $emb->id,
                                    ])
                                    @style(['border-color:#B76E79;background-color:#FBF1F3' => $packagingId === $emb->id])>
                                <div class="h-16 overflow-hidden rounded-lg bg-stone-100">
                                    @if ($embImg)
                                        <img src="{{ $embImg }}" alt="{{ $emb->nom }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-stone-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-2 truncate text-xs font-semibold text-stone-800">{{ $emb->nom }}</p>
                                <p class="text-[11px] text-stone-400">{{ $emb->prix > 0 ? '+' . number_format($emb->prix / 100, 0, ',', ' ') . ' DA' : 'Gratuit' }}</p>
                                @if ($packagingId === $emb->id)
                                    <span class="absolute right-2 top-2 flex h-5 w-5 items-center justify-center rounded-full text-white" style="background-color:#B76E79;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Récapitulatif --}}
            <div class="lg:col-span-1">
                <div class="sticky top-20 rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 text-base font-bold text-stone-900">Récapitulatif</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-stone-600">
                            <span>Sous-total</span>
                            <span>{{ number_format($sousTotal / 100, 0, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between text-stone-600">
                            <span>Emballage</span>
                            <span>{{ $frais > 0 ? '+' . number_format($frais / 100, 0, ',', ' ') . ' DA' : 'Gratuit' }}</span>
                        </div>
                        <div class="my-3 border-t border-stone-100"></div>
                        <div class="flex justify-between text-base font-bold text-stone-900">
                            <span>Total</span>
                            <span>{{ number_format($total / 100, 0, ',', ' ') }} DA</span>
                        </div>
                    </div>
                    <button wire:click="commander" class="mt-5 w-full rounded-xl py-3 text-sm font-semibold text-white transition hover:opacity-90" style="background-color:#B76E79;">
                        Passer la commande
                    </button>
                    <a href="{{ route('shop.catalog') }}" class="mt-2 block text-center text-xs text-stone-400 hover:text-stone-600">Continuer mes achats</a>
                </div>
            </div>
        </div>
    @endif
</div>
