<x-shop-layout :title="'Assistante Naaqati'">
    <div class="mx-auto max-w-md text-center">
        <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl text-white shadow-lg" style="background:linear-gradient(135deg,#C9A45E,#7C5827);">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5h8M8 14h5m-9 6l3.5-2H17a3 3 0 003-3V7a3 3 0 00-3-3H7a3 3 0 00-3 3v13z"/></svg>
        </div>
        <h1 class="text-2xl font-extrabold text-stone-900">Votre conseillère beauté</h1>
        <p class="mt-2 text-sm text-stone-500">
            Bientôt, décrivez votre besoin (cheveux secs, peau sensible, idée cadeau…)
            et notre assistante vous recommandera les produits Naaqati adaptés,
            à ajouter au panier en un clic.
        </p>
        <span class="mt-5 inline-block rounded-full bg-stone-100 px-4 py-1.5 text-xs font-medium text-stone-500">
            ✨ Disponible très prochainement
        </span>
        <div class="mt-8">
            <a href="{{ route('shop.catalog') }}" class="inline-block rounded-xl px-6 py-3 text-sm font-semibold text-white" style="background-color:#A1763C;">
                Voir les produits
            </a>
        </div>
    </div>
</x-shop-layout>
