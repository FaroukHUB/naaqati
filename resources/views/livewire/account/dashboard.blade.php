<div
    x-data="{ toast: '', show: false }"
    x-on:flash.window="toast = $event.detail.message; show = true; clearTimeout(window._t); window._t = setTimeout(() => show = false, 2000)"
>
    <div x-show="show" x-transition style="background-color:#A1763C;"
         class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 rounded-full px-5 py-2.5 text-sm font-medium text-white shadow-lg" x-cloak>
        <span x-text="toast"></span>
    </div>

    @php
        $statutCouleurs = [
            'recue' => 'bg-stone-100 text-stone-600',
            'en_preparation' => 'bg-amber-100 text-amber-700',
            'prete' => 'bg-sky-100 text-sky-700',
            'recuperee' => 'bg-emerald-100 text-emerald-700',
            'terminee' => 'bg-emerald-100 text-emerald-700',
            'annulee' => 'bg-red-100 text-red-600',
        ];
        $cls = 'w-full rounded-xl border-2 border-stone-200 bg-white px-4 py-3 text-base text-stone-900 placeholder-stone-400 shadow-sm outline-none transition focus:border-[#A1763C]';
    @endphp

    {{-- En-tête --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-900">Bonjour {{ $customer->nom }} 👋</h1>
            <p class="text-sm text-stone-500">{{ $customer->telephone }}</p>
        </div>
        <form method="POST" action="{{ route('shop.logout') }}">
            @csrf
            <button type="submit" class="rounded-xl border border-stone-200 bg-white px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-50">
                Déconnexion
            </button>
        </form>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Mes informations --}}
        <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-base font-bold text-stone-900">Mes informations</h2>
            <form wire:submit="enregistrer" class="space-y-3">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-stone-700">Nom complet</label>
                    <input type="text" wire:model="nom" class="{{ $cls }}">
                    @error('nom') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-stone-700">Email</label>
                    <input type="email" wire:model="email" class="{{ $cls }}">
                    @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-stone-700">Adresse <span class="font-normal text-stone-400">(optionnel)</span></label>
                    <input type="text" wire:model="adresse" class="{{ $cls }}">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-stone-700">Ville <span class="font-normal text-stone-400">(optionnel)</span></label>
                    <input type="text" wire:model="ville" class="{{ $cls }}">
                </div>
                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input type="checkbox" wire:model="newsletter" class="rounded border-stone-300 text-[#A1763C] focus:ring-0">
                    Recevoir la newsletter (nouveautés &amp; offres)
                </label>
                <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90" style="background-color:#A1763C;">
                    Enregistrer
                </button>
            </form>
        </div>

        {{-- Mes commandes --}}
        <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-base font-bold text-stone-900">Mes commandes</h2>
            @if ($commandes->isEmpty())
                <p class="text-sm text-stone-400">Vous n'avez pas encore de commande.</p>
                <a href="{{ route('shop.catalog') }}" wire:navigate class="mt-3 inline-block rounded-xl px-4 py-2 text-sm font-semibold text-white" style="background-color:#A1763C;">Découvrir la boutique</a>
            @else
                <div class="space-y-3">
                    @foreach ($commandes as $cmd)
                        <a href="{{ route('shop.confirmation', $cmd->numero) }}"
                           class="flex items-center justify-between rounded-xl border border-stone-100 p-3 transition hover:bg-stone-50">
                            <div>
                                <p class="text-sm font-semibold text-stone-800">{{ $cmd->numero }}</p>
                                <p class="text-xs text-stone-400">{{ $cmd->created_at->isoFormat('D MMM YYYY') }} · {{ $cmd->items->count() }} article(s)</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $statutCouleurs[$cmd->statut->value] ?? 'bg-stone-100 text-stone-600' }}">{{ $cmd->statut->label() }}</span>
                                <p class="mt-1 text-sm font-bold text-stone-800">{{ number_format($cmd->total / 100, 0, ',', ' ') }} DA</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Nouveautés / suggestions --}}
    @if ($nouveautes->isNotEmpty())
        <div class="mt-8">
            <h2 class="mb-4 text-lg font-bold text-stone-900">Nouveautés pour vous ✨</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($nouveautes as $produit)
                    @php $img = $produit->getFirstMediaUrl('images'); $prix = $produit->prixPourRelais($relaisId); @endphp
                    <a href="{{ route('shop.product', $produit->slug) }}" wire:navigate
                       class="group flex flex-col overflow-hidden rounded-2xl border border-stone-100 bg-white shadow-sm transition hover:shadow-md">
                        <div class="aspect-square w-full overflow-hidden bg-stone-100">
                            @if ($img)
                                <img src="{{ $img }}" class="h-full w-full object-cover transition group-hover:scale-105" alt="{{ $produit->nom }}">
                            @endif
                        </div>
                        <div class="p-2">
                            <h3 class="truncate text-xs font-semibold text-stone-800">{{ $produit->nom }}</h3>
                            <p class="text-xs font-bold" style="color:#A1763C;">{{ number_format($prix / 100, 0, ',', ' ') }} DA</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
