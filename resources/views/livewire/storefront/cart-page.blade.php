<div
    x-data="{ toast: '', show: false }"
    x-on:flash.window="toast = $event.detail.message; show = true; clearTimeout(window._t); window._t = setTimeout(() => show = false, 1800)"
>
    <div x-show="show" x-transition style="background-color:#B76E79;"
         class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 rounded-full px-5 py-2.5 text-sm font-medium text-white shadow-lg" x-cloak>
        <span x-text="toast"></span>
    </div>

    <h1 class="mb-2 text-2xl font-bold text-stone-900">Mon panier</h1>

    @php $vide = $packages->sum(fn ($p) => $p['lines']->count()) === 0; @endphp

    @if ($vide)
        <div class="rounded-2xl border border-dashed border-stone-200 bg-white p-12 text-center">
            <p class="text-stone-400">Votre panier est vide.</p>
            <a href="{{ route('shop.catalog') }}" wire:navigate class="mt-4 inline-block rounded-xl px-5 py-2.5 text-sm font-semibold text-white" style="background-color:#B76E79;">Voir les produits</a>
        </div>
    @else
        <p class="mb-5 text-sm text-stone-500">Vous offrez à plusieurs personnes ? Créez un paquet par destinataire, chacun avec son emballage. 🎁</p>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-5 lg:col-span-2">
                @foreach ($packages as $package)
                    <div class="rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                        {{-- En-tête paquet --}}
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="flex items-center gap-2 text-sm font-bold text-stone-800">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs text-white" style="background-color:#B76E79;">{{ $package['index'] }}</span>
                                Paquet {{ $package['index'] }}
                                @if ($package['destinataire']) <span class="font-normal text-stone-400">· pour {{ $package['destinataire'] }}</span> @endif
                            </h2>
                            @if ($packages->count() > 1)
                                <button wire:click="supprimerPaquet('{{ $package['key'] }}')" class="text-xs text-stone-400 hover:text-red-500">Supprimer</button>
                            @endif
                        </div>

                        {{-- Articles du paquet --}}
                        @if ($package['lines']->isEmpty())
                            <p class="rounded-xl bg-stone-50 py-4 text-center text-xs text-stone-400">Aucun article — déplacez-en ici ou ajoutez-en depuis la boutique.</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($package['lines'] as $line)
                                    @php $produit = $line['product']; $img = $produit->getFirstMediaUrl('images'); @endphp
                                    <div class="flex items-center gap-3 rounded-xl border border-stone-100 p-2">
                                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-stone-100">
                                            @if ($img) <img src="{{ $img }}" class="h-full w-full object-cover" alt=""> @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-semibold text-stone-800">{{ $produit->nom }}</p>
                                            <p class="text-xs text-stone-500">{{ number_format($line['prix_unitaire'] / 100, 0, ',', ' ') }} DA</p>
                                            @if ($packages->count() > 1)
                                                <select wire:change="deplacer({{ $produit->id }}, '{{ $package['key'] }}', $event.target.value)"
                                                        class="mt-1 rounded-lg border border-stone-200 py-1 pl-2 pr-6 text-[11px] text-stone-500 outline-none focus:border-[#B76E79]">
                                                    <option value="">Déplacer vers…</option>
                                                    @foreach ($packages as $autre)
                                                        @if ($autre['key'] !== $package['key'])
                                                            <option value="{{ $autre['key'] }}">Paquet {{ $autre['index'] }}{{ $autre['destinataire'] ? ' · '.$autre['destinataire'] : '' }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <button wire:click="changeQuantite('{{ $package['key'] }}', {{ $produit->id }}, -1)" class="h-7 w-7 rounded-full border border-stone-200 text-stone-600 hover:bg-stone-50">−</button>
                                            <span class="w-5 text-center text-sm font-semibold">{{ $line['quantite'] }}</span>
                                            <button wire:click="changeQuantite('{{ $package['key'] }}', {{ $produit->id }}, 1)" class="h-7 w-7 rounded-full border border-stone-200 text-stone-600 hover:bg-stone-50">+</button>
                                        </div>
                                        <button wire:click="supprimer('{{ $package['key'] }}', {{ $produit->id }})" class="text-stone-300 hover:text-red-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Emballage du paquet --}}
                            <div class="mt-3">
                                <p class="mb-2 text-xs font-semibold text-stone-600">Emballage de ce paquet</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($emballages as $emb)
                                        @php $embImg = $emb->getFirstMediaUrl('image'); $sel = $package['packaging_id'] === $emb->id; @endphp
                                        <button wire:click="choisirEmballage('{{ $package['key'] }}', {{ $emb->id }})"
                                                @class(['relative w-24 overflow-hidden rounded-xl border-2 p-1.5 text-left transition', 'border-stone-200 hover:border-stone-300' => !$sel])
                                                @style(['border-color:#B76E79;background-color:#FBF1F3' => $sel])>
                                            <div class="h-12 overflow-hidden rounded-lg bg-stone-100">
                                                @if ($embImg) <img src="{{ $embImg }}" class="h-full w-full object-cover" alt=""> @else <div class="flex h-full items-center justify-center text-stone-300"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25"/></svg></div> @endif
                                            </div>
                                            <p class="mt-1 truncate text-[11px] font-semibold text-stone-700">{{ $emb->nom }}</p>
                                            <p class="text-[10px] text-stone-400">{{ $emb->prix > 0 ? '+'.number_format($emb->prix / 100, 0, ',', ' ').' DA' : 'Gratuit' }}</p>
                                            @if ($sel)
                                                <span class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full text-white" style="background-color:#B76E79;"><svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Destinataire + message --}}
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                <input type="text" wire:model.blur="dest.{{ $package['key'] }}" placeholder="Pour qui ? (optionnel)"
                                       class="rounded-xl border-2 border-stone-200 bg-white px-3 py-2 text-sm outline-none focus:border-[#B76E79]">
                                <input type="text" wire:model.blur="msg.{{ $package['key'] }}" placeholder="Petit mot / carte (optionnel)"
                                       class="rounded-xl border-2 border-stone-200 bg-white px-3 py-2 text-sm outline-none focus:border-[#B76E79]">
                            </div>

                            <p class="mt-3 text-right text-xs text-stone-500">Sous-total paquet : <span class="font-semibold text-stone-700">{{ number_format($package['total'] / 100, 0, ',', ' ') }} DA</span></p>
                        @endif
                    </div>
                @endforeach

                <button wire:click="ajouterPaquet" class="flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-stone-300 py-3 text-sm font-semibold text-stone-500 transition hover:border-[#B76E79] hover:text-[#B76E79]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Nouveau paquet cadeau
                </button>
            </div>

            {{-- Récapitulatif --}}
            <div class="lg:col-span-1">
                <div class="sticky top-20 rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                    <h2 class="mb-4 text-base font-bold text-stone-900">Récapitulatif</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-stone-600"><span>Articles</span><span>{{ number_format($sousTotal / 100, 0, ',', ' ') }} DA</span></div>
                        <div class="flex justify-between text-stone-600"><span>Emballages</span><span>{{ $frais > 0 ? '+'.number_format($frais / 100, 0, ',', ' ').' DA' : 'Gratuit' }}</span></div>
                        <div class="my-3 border-t border-stone-100"></div>
                        <div class="flex justify-between text-base font-bold text-stone-900"><span>Total</span><span>{{ number_format($total / 100, 0, ',', ' ') }} DA</span></div>
                    </div>
                    <button wire:click="commander" class="mt-5 w-full rounded-xl py-3 text-sm font-semibold text-white transition hover:opacity-90" style="background-color:#B76E79;">Passer la commande</button>
                    <a href="{{ route('shop.catalog') }}" wire:navigate class="mt-2 block text-center text-xs text-stone-400 hover:text-stone-600">Continuer mes achats</a>
                </div>
            </div>
        </div>
    @endif
</div>
