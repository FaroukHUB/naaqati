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
                    <label class="mb-2 block text-sm font-semibold text-stone-700">Emballage</label>
                    <select wire:model.live="packagingId" class="w-full rounded-xl border-stone-200 text-sm focus:border-stone-300 focus:ring-0">
                        <option value="">Sachet kraft (gratuit)</option>
                        @foreach ($emballages as $emb)
                            <option value="{{ $emb->id }}">
                                {{ $emb->nom }} @if ($emb->prix > 0) (+{{ number_format($emb->prix / 100, 0, ',', ' ') }} DA) @else (gratuit) @endif
                            </option>
                        @endforeach
                    </select>
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
