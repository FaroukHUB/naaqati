<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-rocket-launch" class="h-5 w-5 text-primary-500" />
                <h3 class="text-base font-bold">Publier un produit en 3 étapes</h3>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-100 p-3 dark:border-gray-700">
                    <p class="text-sm font-semibold">1. Créer le produit</p>
                    <p class="mt-1 text-xs text-gray-500">Catalogue → Produits → Créer. Renseigne le <strong>Stock initial à Riadi City</strong> directement dans le formulaire.</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3 dark:border-gray-700">
                    <p class="text-sm font-semibold">2. Vérifier qu'il est actif</p>
                    <p class="mt-1 text-xs text-gray-500">Le produit doit être <strong>Actif</strong> et avoir du <strong>stock</strong> pour s'afficher.</p>
                </div>
                <div class="rounded-xl border border-gray-100 p-3 dark:border-gray-700">
                    <p class="text-sm font-semibold">3. Voir le résultat</p>
                    <p class="mt-1 text-xs text-gray-500">Bouton <strong>« Voir le site »</strong> (menu de gauche) ou <strong>« Voir »</strong> sur le produit.</p>
                </div>
            </div>

            @if ($produitsInvisibles > 0 || $categoriesVides > 0)
                <div class="space-y-2">
                    @if ($produitsInvisibles > 0)
                        <div class="flex items-start gap-2 rounded-xl bg-amber-50 p-3 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                            <x-filament::icon icon="heroicon-o-exclamation-triangle" class="mt-0.5 h-5 w-5 shrink-0" />
                            <span><strong>{{ $produitsInvisibles }}</strong> produit(s) actif(s) <strong>sans stock disponible</strong> — ils n'apparaissent pas (ou « Épuisé ») sur la boutique. Ajoute-leur du stock dans <strong>Stock</strong>.</span>
                        </div>
                    @endif
                    @if ($categoriesVides > 0)
                        <div class="flex items-start gap-2 rounded-xl bg-gray-50 p-3 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            <x-filament::icon icon="heroicon-o-information-circle" class="mt-0.5 h-5 w-5 shrink-0" />
                            <span><strong>{{ $categoriesVides }}</strong> catégorie(s) active(s) <strong>sans produit</strong> — elles n'apparaissent pas sur l'accueil tant qu'elles sont vides.</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="flex items-center gap-2 rounded-xl bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
                    <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 shrink-0" />
                    <span>Tout est en ordre : tes produits actifs ont du stock. 🎉</span>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
