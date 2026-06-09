<div
    x-data="{ toast: '', show: false }"
    x-on:flash.window="toast = $event.detail.message; show = true; clearTimeout(window._t); window._t = setTimeout(() => show = false, 1800)"
>
    {{-- Toast --}}
    <div x-show="show" x-transition style="background-color:#B76E79;"
         class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 rounded-full px-5 py-2.5 text-sm font-medium text-white shadow-lg"
         x-cloak>
        <span x-text="toast"></span>
    </div>

    @php
        $gradients = [
            'linear-gradient(135deg,#C98B96,#9B5563)',
            'linear-gradient(135deg,#B89B72,#8C6E45)',
            'linear-gradient(135deg,#9CA98B,#5F7350)',
            'linear-gradient(135deg,#A88BC9,#6E5586)',
            'linear-gradient(135deg,#C9A88B,#8C6745)',
            'linear-gradient(135deg,#8BB6C9,#506E83)',
        ];
    @endphp

    @if (! $modeProduits)
        {{-- ===== MODE GRILLE CATÉGORIES ===== --}}

        {{-- Bannière --}}
        <div class="mb-5 overflow-hidden rounded-3xl px-6 py-7 text-white shadow-sm" style="background:linear-gradient(135deg,#C98B96 0%,#B76E79 45%,#9B5563 100%);">
            <p class="text-xs font-medium uppercase tracking-wider text-white/70">Cosmétiques naturels</p>
            <h1 class="mt-1 text-2xl font-extrabold leading-tight">Bienvenue chez Naaqati</h1>
            <p class="mt-1 text-sm text-white/90">Choisissez une catégorie, récupérez à Riadi City. 🌿</p>
        </div>

        {{-- Recherche --}}
        <div class="mb-5">
            <input type="search" wire:model.live.debounce.400ms="search"
                   placeholder="Rechercher un produit…"
                   class="w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-stone-300 focus:ring-0">
        </div>

        @if ($categories->isEmpty())
            <div class="rounded-2xl border border-dashed border-stone-200 bg-white p-12 text-center text-stone-400">
                Aucune catégorie disponible pour le moment.
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach ($categories as $i => $cat)
                    @php $img = $cat->getFirstMediaUrl('image'); @endphp
                    <button wire:click="ouvrirCategorie({{ $cat->id }})"
                            class="group relative aspect-square overflow-hidden rounded-2xl text-left shadow-sm transition hover:shadow-md"
                            style="{{ $img ? '' : 'background:' . $gradients[$i % count($gradients)] . ';' }}">
                        @if ($img)
                            <img src="{{ $img }}" alt="{{ $cat->nom }}" class="absolute inset-0 h-full w-full object-cover transition group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 p-3">
                            <h3 class="text-sm font-bold leading-tight text-white drop-shadow">{{ $cat->nom }}</h3>
                            <p class="text-[11px] text-white/80">{{ $cat->dispo_count }} produit{{ $cat->dispo_count > 1 ? 's' : '' }}</p>
                        </div>
                    </button>
                @endforeach
            </div>
        @endif

    @else
        {{-- ===== MODE PRODUITS ===== --}}

        {{-- Barre retour + titre --}}
        <div class="mb-4 flex items-center gap-3">
            <button wire:click="retour" class="flex h-9 w-9 items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 hover:bg-stone-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h1 class="text-xl font-bold text-stone-900">
                {{ $currentCategory?->nom ?? 'Résultats' }}
            </h1>
        </div>

        {{-- Recherche --}}
        <div class="mb-5">
            <input type="search" wire:model.live.debounce.400ms="search"
                   placeholder="Rechercher un produit…"
                   class="w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-stone-300 focus:ring-0">
        </div>

        @if ($produits->isEmpty())
            <div class="rounded-2xl border border-dashed border-stone-200 bg-white p-12 text-center text-stone-400">
                Aucun produit trouvé.
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
                        <a href="{{ route('shop.product', $produit->slug) }}" wire:navigate class="block aspect-square w-full overflow-hidden bg-stone-100">
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $produit->nom }}" class="h-full w-full object-cover transition group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-stone-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                                </div>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col p-3">
                            <p class="text-xs text-stone-400">{{ $produit->category->nom }}</p>
                            <a href="{{ route('shop.product', $produit->slug) }}" wire:navigate class="text-sm font-semibold leading-tight text-stone-800 hover:underline">{{ $produit->nom }}</a>
                            <p class="mt-1 text-base font-bold" style="color:#B76E79;">
                                {{ number_format($prix / 100, 0, ',', ' ') }} DA
                            </p>
                            <div class="mt-auto pt-3">
                                @if ($dispo > 0)
                                    <button wire:click="ajouter({{ $produit->id }})" wire:loading.attr="disabled"
                                            class="w-full rounded-xl py-2 text-sm font-semibold text-white transition hover:opacity-90" style="background-color:#B76E79;">
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
    @endif
</div>
