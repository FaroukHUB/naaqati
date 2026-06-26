<x-filament-panels::page>
    <div class="mx-auto max-w-3xl space-y-4">

        <x-filament::section icon="heroicon-o-cube" icon-color="primary">
            <x-slot name="heading">Ajouter un produit et le rendre visible</x-slot>
            <ol class="list-decimal space-y-1 pl-5 text-sm text-gray-600 dark:text-gray-300">
                <li><strong>Catalogue → Produits → Créer</strong>.</li>
                <li>Renseigne le nom, la catégorie, le prix, une photo, et surtout le <strong>Stock initial à Riadi City</strong>.</li>
                <li>Laisse <strong>Produit actif</strong> coché, puis <strong>Créer</strong>.</li>
                <li>Le produit apparaît directement sur la boutique. 🎉</li>
            </ol>
            <p class="mt-2 text-xs text-gray-400">Un produit n'apparaît que s'il est <strong>actif</strong> ET a du <strong>stock &gt; 0</strong>. La colonne « Sur la boutique » te le confirme (✅/❌).</p>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-archive-box" icon-color="warning">
            <x-slot name="heading">Modifier le stock (réapprovisionner)</x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-300">Dans <strong>Catalogue → Produits</strong>, clique sur le bouton <strong>« Stock »</strong> de la ligne du produit (ou en haut de sa fiche), saisis la nouvelle quantité et enregistre.</p>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-shopping-bag" icon-color="primary">
            <x-slot name="heading">Gérer une commande et prévenir la cliente</x-slot>
            <ol class="list-decimal space-y-1 pl-5 text-sm text-gray-600 dark:text-gray-300">
                <li><strong>Ventes → Commandes</strong>, ouvre la commande.</li>
                <li>Bouton <strong>« Changer le statut »</strong> → passe-la en <strong>Prête à récupérer</strong> (le stock se met à jour tout seul).</li>
                <li>Un bouton vert <strong>« Envoyer le WhatsApp »</strong> apparaît → clique : WhatsApp s'ouvre avec le message + l'adresse pré-remplis. Tu n'as qu'à envoyer.</li>
            </ol>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-map-pin" icon-color="primary">
            <x-slot name="heading">Adresse & message WhatsApp</x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Réglages → Point relais</strong> : adresse, codes (portail, porte d'entrée, ascenseur), étage, instructions d'accès.<br>
            <strong>Réglages → Message WhatsApp</strong> : le texte envoyé aux clientes (avec les variables {{ '{{adresse}}' }}, {{ '{{code_porte}}' }}…).</p>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-user-circle" icon-color="primary">
            <x-slot name="heading">Créer un compte administrateur</x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Réglages → Administrateurs → Créer</strong> : nom, email, mot de passe. La personne se connecte ensuite sur <strong>/admin</strong>.</p>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-globe-alt" icon-color="primary">
            <x-slot name="heading">Voir la boutique</x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-300">Bouton <strong>« Voir le site »</strong> (menu de gauche) pour ouvrir la boutique dans un nouvel onglet, ou <strong>« Voir »</strong> sur un produit.</p>
        </x-filament::section>

    </div>
</x-filament-panels::page>
