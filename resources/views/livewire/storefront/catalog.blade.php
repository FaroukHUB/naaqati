<div
    x-data="{ toast: '', show: false }"
    x-on:flash.window="toast = $event.detail.message; show = true; clearTimeout(window._t); window._t = setTimeout(() => show = false, 1800)"
>
    {{-- Toast --}}
    <div x-show="show" x-transition style="background-color:#B76E79;"
         class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 rounded-full px-5 py-2.5 text-sm font-medium text-white shadow-lg"
         x-cloak>
        <span x-text="toast"></span>
    </div>

    {{-- Bannière d'accueil --}}
    <div class="mb-5 overflow-hidden rounded-3xl px-6 py-7 text-white shadow-sm" style="background:linear-gradient(135deg,#C98B96 0%,#B76E79 45%,#9B5563 100%);">
        <p class="text-xs font-medium uppercase tracking-wider text-white/70">Cosmétiques naturels</p>
        <h1 class="mt-1 text-2xl font-extrabold leading-tight">Bienvenue chez Naaqati</h1>
        <p class="mt-1 text-sm text-white/90">Choisissez vos produits, récupérez-les à Riadi City. 🌿</p>
    </div>

    <div class="mb-5">
        <input type="search" wire:model.live.debounce.400ms="search"
               placeholder="Rechercher un produit…"
               class="w-full rounded-xl border-stone-200 bg-white px-4 py-2.5 text-sm shadow-sm focus:border-stone-300 focus:ring-0">
    </div>

    {{-- Filtres catégories --}}
    <div class="mb-6 flex flex-wrap gap-2">
        <button wire:click="$set('categoryId', null)"
                @class([
                    'rounded-full px-3 py-1.5 text-sm font-medium transition',
                    'text-white' => $categoryId === null,
                    'bg-white text-stone-600 border border-stone-200 hover:border-stone-300' => $categoryId !== null,
                ])
                @style(['background-color:#B76E79' => $categoryId === null])>
            Tout
        </button>
        @foreach ($categories as $cat)
            <button wire:click="$set('categoryId', {{ $cat->id }})"
                    @class([
                        'rounded-full px-3 py-1.5 text-sm font-medium transition',
                        'text-white' => $categoryId === $cat->id,
                        'bg-white text-stone-600 border border-stone-200 hover:border-stone-300' => $categoryId !== $cat->id,
                    ])
                    @style(['background-color:#B76E79' => $categoryId === $cat->id])>
                {{ $cat->nom }}
            </button>
        @endforeach
    </div>

    {{-- Grille produits --}}
    @if ($produits->isEmpty())
        <div class="rounded-2xl border border-dashed border-stone-200 bg-white p-12 text-center text-stone-400">
            Aucun produit disponible pour le moment.
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($produits as $produit)
                @php
                    $inv = $produit->inventories->first();
                    $dispo = $inv?->stock_disponible ?? 0;
                    $prix = $produit->prixPourRelais($relaisId);
                    $image = $produit->getFirstMediaUrl('images');
                @endphp
                <div class="group flex flex-col overflow-hidden rounded-2xl border border-stone-100 bg-white shadow-sm transition hover:shadow-md">
                    <div class="aspect-square w-full overflow-hidden bg-stone-100">
                        @if ($image)
                            <img src="{{ $image }}" alt="{{ $produit->nom }}" class="h-full w-full object-cover transition group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-stone-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col p-3">
                        <p class="text-xs text-stone-400">{{ $produit->category->nom }}</p>
                        <h3 class="text-sm font-semibold text-stone-800 leading-tight">{{ $produit->nom }}</h3>
                        <p class="mt-1 text-base font-bold" style="color:#B76E79;">
                            {{ number_format($prix / 100, 0, ',', ' ') }} DA
                        </p>
                        <div class="mt-auto pt-3">
                            @if ($dispo > 0)
                                <button wire:click="ajouter({{ $produit->id }})"
                                        wire:loading.attr="disabled"
                                        class="w-full rounded-xl py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                        style="background-color:#B76E79;">
                                    Ajouter
                                </button>
                            @else
                                <button disabled class="w-full rounded-xl bg-stone-100 py-2 text-sm font-medium text-stone-400">
                                    Épuisé
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
