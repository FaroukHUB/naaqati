<div
    x-data="{ toast: '', show: false }"
    x-on:flash.window="toast = $event.detail.message; show = true; clearTimeout(window._t); window._t = setTimeout(() => show = false, 1800)"
>
    {{-- Toast --}}
    <div x-show="show" x-transition style="background-color:#A1763C;"
         class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 rounded-full px-5 py-2.5 text-sm font-medium text-white shadow-lg" x-cloak>
        <span x-text="toast"></span>
    </div>

    @php
        $gradients = [
            'linear-gradient(135deg,#C9A45E,#7C5827)',
            'linear-gradient(135deg,#B89B72,#8C6E45)',
            'linear-gradient(135deg,#C9A88B,#8C6745)',
            'linear-gradient(135deg,#A1763C,#5F4220)',
        ];
    @endphp

    @if (! $modeProduits)
        {{-- ====================== ACCUEIL ====================== --}}

        {{-- 1. HERO --}}
        <section
            x-data="{ s: 0, n: {{ max($heroSlides->count(), 1) }} }"
            x-init="if (n > 1) setInterval(() => s = (s + 1) % n, 6000)"
            class="relative -mt-6 mb-10 h-[80vh] min-h-[480px] max-h-[720px] overflow-hidden"
            style="background:linear-gradient(135deg,#C9A45E,#7C5827); width:100vw; margin-left:calc(50% - 50vw);"
        >
            @if ($heroSlides->isEmpty())
                <div class="flex h-full flex-col justify-center px-7 text-white" style="background:linear-gradient(135deg,#C9A45E 0%,#A1763C 45%,#7C5827 100%);">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-white/70">Produits du Sahara</p>
                    <h1 class="mt-3 text-4xl font-extrabold leading-tight">Bienvenue chez Naaqati</h1>
                    <p class="mt-3 max-w-md text-base text-white/90">Cosmétiques naturels du Sahara — savons, huiles, muscs, henné… à récupérer à Riadi City.</p>
                </div>
            @else
                @foreach ($heroSlides as $i => $slide)
                    @php $img = $slide->getFirstMediaUrl('image'); @endphp
                    <div class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                         :class="s === {{ $i }} ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                         style="{{ $img ? "background-image:url('{$img}');background-size:cover;background-position:center;" : 'background:linear-gradient(135deg,#C9A45E,#7C5827);' }}">
                        <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(45,30,12,0.78) 0%, rgba(45,30,12,0.15) 55%, rgba(45,30,12,0.05) 100%);"></div>
                        <div class="relative flex h-full flex-col justify-end px-7 pb-16 text-white">
                            <div class="max-w-lg">
                                @if ($slide->titre)<h1 class="text-3xl font-extrabold leading-tight drop-shadow sm:text-4xl">{{ $slide->titre }}</h1>@endif
                                @if ($slide->sous_titre)<p class="mt-2 text-base text-white/90 drop-shadow">{{ $slide->sous_titre }}</p>@endif
                                @if ($slide->bouton_texte)
                                    <a href="{{ $slide->lien ?: route('shop.catalog') }}" class="mt-5 inline-block rounded-xl bg-white px-6 py-3 text-sm font-semibold shadow" style="color:#7C5827;">{{ $slide->bouton_texte }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                @if ($heroSlides->count() > 1)
                    <div class="absolute bottom-6 left-1/2 z-10 flex -translate-x-1/2 gap-2">
                        @foreach ($heroSlides as $i => $slide)
                            <button @click="s = {{ $i }}" class="h-2 rounded-full transition-all" :class="s === {{ $i }} ? 'w-6 bg-white' : 'w-2 bg-white/50'"></button>
                        @endforeach
                    </div>
                @endif
            @endif
        </section>

        {{-- Recherche --}}
        <div class="mb-10">
            <input type="search" wire:model.live.debounce.400ms="search" placeholder="Rechercher un produit…"
                   class="w-full rounded-2xl border-2 border-stone-200 bg-white px-4 py-3 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#A1763C]">
        </div>

        {{-- 2. NOTRE SÉLECTION DU MOMENT --}}
        @if ($enAvant->isNotEmpty())
            <section class="mb-12">
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-[0.2em]" style="color:#A1763C;">Coup de cœur</span>
                        <h2 class="text-lg font-bold text-stone-900">Notre sélection du moment</h2>
                    </div>
                </div>
                <div class="-mx-4 flex gap-4 overflow-x-auto px-4 pb-2">
                    @foreach ($enAvant as $produit)
                        @php $img = $produit->getFirstMediaUrl('images'); $prix = $produit->prixPourRelais($relaisId); @endphp
                        <div class="w-40 shrink-0">
                            <a href="{{ route('shop.product', $produit->slug) }}" wire:navigate class="block aspect-square w-full overflow-hidden rounded-2xl bg-stone-100">
                                @if ($img)<img src="{{ $img }}" class="h-full w-full object-cover" alt="{{ $produit->nom }}">@endif
                            </a>
                            <h3 class="mt-2 truncate text-sm font-semibold text-stone-800">{{ $produit->nom }}</h3>
                            <p class="text-sm font-bold" style="color:#A1763C;">{{ number_format($prix / 100, 0, ',', ' ') }} DA</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- 3. COMMENT ÇA MARCHE --}}
        <section class="mb-12 rounded-3xl bg-[#FBF7F0] p-6">
            <h2 class="mb-5 text-center text-lg font-bold text-stone-900">Comment ça marche ?</h2>
            <div class="grid grid-cols-3 gap-3 text-center">
                @foreach ([['🛍️','Choisissez','Parcourez nos produits naturels et ajoutez-les au panier.'],['📅','Réservez','Indiquez quand vous passez récupérer à Riadi City.'],['🔔','On vous prévient','Un message WhatsApp dès que votre commande est prête.']] as $etape)
                    <div>
                        <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-white text-2xl shadow-sm">{{ $etape[0] }}</div>
                        <h3 class="text-sm font-semibold text-stone-800">{{ $etape[1] }}</h3>
                        <p class="mt-1 text-xs text-stone-500">{{ $etape[2] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- 4. CATÉGORIES --}}
        @if ($categories->isNotEmpty())
            <section class="mb-12">
                <h2 class="mb-4 text-lg font-bold text-stone-900">Nos catégories</h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach ($categories as $i => $cat)
                        @php $img = $cat->getFirstMediaUrl('image'); @endphp
                        <button wire:click="ouvrirCategorie({{ $cat->id }})" class="group relative aspect-square overflow-hidden rounded-2xl text-left shadow-sm transition hover:shadow-md"
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
            </section>
        @endif

        {{-- 5. RECHERCHE PAR BESOIN --}}
        @if ($concerns->isNotEmpty())
            <section class="mb-12">
                <div class="mb-5 text-center">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em]" style="color:#A1763C;">Sur-mesure</span>
                    <h2 class="mt-1 text-xl font-extrabold text-stone-900">Trouvez selon votre besoin</h2>
                    <p class="mt-1 text-sm text-stone-500">Choisissez ce qui vous correspond, on vous montre les produits adaptés.</p>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($concerns as $c)
                        <button wire:click="ouvrirBesoin('{{ $c->slug }}')"
                                class="group relative overflow-hidden rounded-3xl border border-[#EADFCE] p-5 text-left transition hover:-translate-y-1 hover:shadow-xl"
                                style="background:linear-gradient(150deg,#FFFFFF 0%,#FBF7F0 60%,#F3E9D6 100%);">
                            <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full" style="background:#A1763C;opacity:0.05;"></div>
                            @php $ic = \Illuminate\Support\Str::startsWith((string) $c->emoji, 'heroicon-') ? $c->emoji : 'heroicon-o-sparkles'; @endphp
                            <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-[#EADFCE]" style="color:#A1763C;">
                                @svg($ic, 'h-6 w-6')
                            </div>
                            <h3 class="relative mt-4 text-sm font-bold leading-tight text-stone-800">{{ $c->nom }}</h3>
                            <span class="relative mt-2 inline-flex items-center gap-1 text-xs font-semibold" style="color:#A1763C;">
                                Découvrir
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </button>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- 6. AVIS CLIENTES --}}
        @if ($avis->isNotEmpty())
            <section class="mb-12 overflow-hidden rounded-3xl border border-[#EADFCE] p-6 sm:p-8" style="background:radial-gradient(120% 120% at 0% 0%, #FBF7F0 0%, #FFFFFF 55%);">
                <div class="mb-6 text-center">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em]" style="color:#A1763C;">Avis clientes</span>
                    <h2 class="mt-1 text-xl font-extrabold text-stone-900">Elles valident Naaqati</h2>
                </div>
                <div class="-mx-2 flex snap-x snap-mandatory gap-4 overflow-x-auto px-2 pb-2">
                    @foreach ($avis as $a)
                        <figure class="relative w-80 shrink-0 snap-center rounded-3xl bg-white p-6 shadow-sm ring-1 ring-stone-100">
                            <svg class="absolute right-5 top-4 h-10 w-10 opacity-10" style="color:#A1763C;" fill="currentColor" viewBox="0 0 24 24"><path d="M7.17 6A5.17 5.17 0 002 11.17V18h6.83v-6.83H5.5A1.67 1.67 0 017.17 9.5V6zm9 0A5.17 5.17 0 0011 11.17V18h6.83v-6.83H14.5a1.67 1.67 0 011.67-1.67V6z"/></svg>
                            <div class="relative flex items-center gap-0.5" style="color:#A1763C;">
                                {!! str_repeat('<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.34 4.12a1 1 0 00.95.69h4.33c.97 0 1.37 1.24.59 1.81l-3.5 2.54a1 1 0 00-.36 1.12l1.33 4.12c.3.92-.75 1.69-1.54 1.12l-3.5-2.54a1 1 0 00-1.18 0l-3.5 2.54c-.79.57-1.84-.2-1.54-1.12l1.33-4.12a1 1 0 00-.36-1.12L2.16 9.55c-.78-.57-.38-1.81.59-1.81h4.33a1 1 0 00.95-.69z"/></svg>', (int) ($a->note ?? 5)) !!}
                            </div>
                            <blockquote class="relative mt-3 text-sm leading-relaxed text-stone-700">« {{ $a->texte }} »</blockquote>
                            <figcaption class="relative mt-4 flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold text-white" style="background:linear-gradient(135deg,#C9A45E,#7C5827);">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($a->nom, 0, 1)) }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-stone-800">{{ $a->nom }}</p>
                                    <p class="inline-flex items-center gap-1 text-[11px] text-emerald-600"><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 111.4-1.4L9 11.6l6.3-6.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg> Cliente vérifiée</p>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- 7. ASSISTANTE NAAQATI --}}
        <section class="mb-12 overflow-hidden rounded-3xl p-6 text-white shadow-sm" style="background:linear-gradient(135deg,#A1763C,#5F4220);">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white/15">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5h8M8 14h5m-9 6l3.5-2H17a3 3 0 003-3V7a3 3 0 00-3-3H7a3 3 0 00-3 3v13z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Votre assistante Naaqati</h2>
                    <p class="mt-1 text-sm text-white/90">Décrivez votre besoin (cheveux secs, peau sensible, idée cadeau…) : notre assistante vous conseille les bons produits et les ajoute au panier en un clic.</p>
                    <a href="{{ route('shop.assistante') }}" class="mt-4 inline-block rounded-xl bg-white px-5 py-2.5 text-sm font-semibold" style="color:#7C5827;">Discuter avec l'assistante</a>
                </div>
            </div>
        </section>

        {{-- 7. NEWSLETTER --}}
        <section class="mb-12 rounded-3xl border-2 border-[#E8D9BF] bg-[#FBF7F0] p-6 text-center">
            <h2 class="text-lg font-bold text-stone-900">Restez informée 🌿</h2>
            <p class="mt-1 mb-4 text-sm text-stone-500">Nouveautés, conseils et offres Naaqati, directement par email.</p>
            <livewire:newsletter-form />
        </section>

    @else
        {{-- ====================== RÉSULTATS PRODUITS ====================== --}}
        <div class="mb-4 flex items-center gap-3">
            <button wire:click="retour" class="flex h-9 w-9 items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 hover:bg-stone-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h1 class="text-xl font-bold text-stone-900">
                {{ $currentCategory?->nom ?? ($currentConcern ? 'Besoin : ' . $currentConcern->nom : 'Résultats') }}
            </h1>
        </div>

        <div class="mb-5">
            <input type="search" wire:model.live.debounce.400ms="search" placeholder="Rechercher un produit…"
                   class="w-full rounded-2xl border-2 border-stone-200 bg-white px-4 py-3 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#A1763C]">
        </div>

        @if ($produits->isEmpty())
            <div class="rounded-2xl border border-dashed border-stone-200 bg-white p-12 text-center text-stone-400">Aucun produit trouvé.</div>
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
                            <p class="mt-1 text-base font-bold" style="color:#A1763C;">{{ number_format($prix / 100, 0, ',', ' ') }} DA</p>
                            <div class="mt-auto pt-3">
                                @if ($dispo > 0)
                                    <button wire:click="ajouter({{ $produit->id }})" wire:loading.attr="disabled" class="w-full rounded-xl py-2 text-sm font-semibold text-white transition hover:opacity-90" style="background-color:#A1763C;">Ajouter</button>
                                @else
                                    <button disabled class="w-full rounded-xl bg-stone-100 py-2 text-sm font-medium text-stone-400">Épuisé</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
