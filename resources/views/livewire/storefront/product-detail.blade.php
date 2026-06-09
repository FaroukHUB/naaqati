<div
    x-data="{ toast: '', show: false, current: 0 }"
    x-on:flash.window="toast = $event.detail.message; show = true; clearTimeout(window._t); window._t = setTimeout(() => show = false, 2000)"
>
    {{-- Toast --}}
    <div x-show="show" x-transition style="background-color:#B76E79;"
         class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 rounded-full px-5 py-2.5 text-sm font-medium text-white shadow-lg" x-cloak>
        <span x-text="toast"></span>
    </div>

    @php $images = $product->getMedia('images'); @endphp

    {{-- Fil d'ariane --}}
    <nav class="mb-4 flex items-center gap-1.5 text-xs text-stone-400">
        <a href="{{ route('shop.catalog') }}" class="hover:text-stone-600">Accueil</a>
        <span>›</span>
        <a href="{{ route('shop.catalog', ['cat' => $product->category_id]) }}" class="hover:text-stone-600">{{ $product->category->nom }}</a>
        <span>›</span>
        <span class="text-stone-600">{{ $product->nom }}</span>
    </nav>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Galerie --}}
        <div>
            <div class="aspect-square w-full overflow-hidden rounded-3xl bg-stone-100">
                @if ($images->isNotEmpty())
                    @foreach ($images as $idx => $media)
                        <img src="{{ $media->getUrl() }}" alt="{{ $product->nom }}"
                             x-show="current === {{ $idx }}" x-transition
                             class="h-full w-full object-cover" @if(!$loop->first) x-cloak @endif>
                    @endforeach
                @else
                    <div class="flex h-full w-full items-center justify-center text-stone-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                    </div>
                @endif
            </div>

            @if ($images->count() > 1)
                <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                    @foreach ($images as $idx => $media)
                        <button type="button" @click="current = {{ $idx }}"
                                class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border-2 transition"
                                :class="current === {{ $idx }} ? 'border-[#B76E79]' : 'border-transparent'">
                            <img src="{{ $media->getUrl() }}" class="h-full w-full object-cover" alt="">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Infos --}}
        <div>
            <a href="{{ route('shop.catalog', ['cat' => $product->category_id]) }}"
               class="inline-block rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-500">{{ $product->category->nom }}</a>

            <h1 class="mt-3 text-2xl font-extrabold leading-tight text-stone-900">{{ $product->nom }}</h1>

            <p class="mt-2 text-2xl font-bold" style="color:#B76E79;">{{ number_format($prix / 100, 0, ',', ' ') }} DA</p>

            @if ($product->desc_courte)
                <p class="mt-3 text-sm leading-relaxed text-stone-600">{{ $product->desc_courte }}</p>
            @endif

            {{-- Stock --}}
            <div class="mt-4">
                @if ($dispo > 0)
                    <span class="inline-flex items-center gap-1.5 text-sm text-emerald-600">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> En stock
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-sm text-red-500">
                        <span class="h-2 w-2 rounded-full bg-red-400"></span> Épuisé
                    </span>
                @endif
            </div>

            {{-- Quantité + ajout --}}
            @if ($dispo > 0)
                <div class="mt-5 flex items-center gap-4">
                    <div class="flex items-center gap-2 rounded-xl border border-stone-200 bg-white p-1">
                        <button type="button" wire:click="changerQuantite(-1)" class="h-9 w-9 rounded-lg text-lg text-stone-600 hover:bg-stone-50">−</button>
                        <span class="w-8 text-center text-sm font-semibold">{{ $quantite }}</span>
                        <button type="button" wire:click="changerQuantite(1)" class="h-9 w-9 rounded-lg text-lg text-stone-600 hover:bg-stone-50">+</button>
                    </div>
                    <button wire:click="ajouter" wire:loading.attr="disabled"
                            class="flex-1 rounded-xl py-3 text-sm font-semibold text-white transition hover:opacity-90 disabled:opacity-50"
                            style="background-color:#B76E79;">
                        Ajouter au panier
                    </button>
                </div>
            @endif

            {{-- Encart retrait --}}
            <div class="mt-5 flex items-start gap-3 rounded-2xl bg-stone-50 p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                <p class="text-xs leading-relaxed text-stone-500">
                    <strong class="text-stone-700">Retrait sur place</strong> au point relais Riadi City. Vous recevrez un message WhatsApp dès que votre commande est prête. Paiement au retrait.
                </p>
            </div>

            {{-- Description longue --}}
            @if ($product->desc_longue)
                <div class="mt-6">
                    <h2 class="mb-2 text-sm font-bold text-stone-900">Description</h2>
                    <div class="prose-sm text-sm leading-relaxed text-stone-600 whitespace-pre-line">{{ $product->desc_longue }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Produits similaires --}}
    @if ($similaires->isNotEmpty())
        <div class="mt-12">
            <h2 class="mb-4 text-lg font-bold text-stone-900">Vous aimerez aussi</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($similaires as $sim)
                    @php $simImg = $sim->getFirstMediaUrl('images'); $simPrix = $sim->prixPourRelais($relaisId); @endphp
                    <a href="{{ route('shop.product', $sim->slug) }}" wire:navigate
                       class="group flex flex-col overflow-hidden rounded-2xl border border-stone-100 bg-white shadow-sm transition hover:shadow-md">
                        <div class="aspect-square w-full overflow-hidden bg-stone-100">
                            @if ($simImg)
                                <img src="{{ $simImg }}" class="h-full w-full object-cover transition group-hover:scale-105" alt="{{ $sim->nom }}">
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="truncate text-sm font-semibold text-stone-800">{{ $sim->nom }}</h3>
                            <p class="mt-1 text-sm font-bold" style="color:#B76E79;">{{ number_format($simPrix / 100, 0, ',', ' ') }} DA</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
