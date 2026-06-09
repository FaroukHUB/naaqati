# Naaqati

Plateforme de commande pour point relais, conçue dès le départ pour évoluer
vers une plateforme **multi-relais, multi-villes, multi-pays et multi-devises**.

- **Phase 1 (actuelle)** : un seul relais — **Riadi City**, prix en **DZD**,
  retrait sur place, notifications WhatsApp via lien `wa.me`.
- **Évolution prévue** : plusieurs relais / villes / pays / devises, stocks et
  horaires séparés par relais, rôles (super admin, admin relais, préparateur,
  lecture seule), automatisation WhatsApp Cloud API, suivi après achat, feedback.

## Stack

| Couche | Choix |
|---|---|
| Framework | Laravel 11 (PHP 8.2+) |
| Admin | Filament v3 |
| Base de données | MySQL/MariaDB (o2switch) — SQLite en dév |
| Rôles | spatie/laravel-permission |
| Photos | spatie/laravel-medialibrary |
| Slugs | spatie/laravel-sluggable |

## Architecture multi-relais (à ne pas casser)

Le multi-relais est l'axe central. Tout le transactionnel porte un `relais_id` :
`inventories`, `orders`, `pickup_schedules`, `schedule_exceptions`, `settings`.

- `App\Support\CurrentRelais` : contexte du relais courant (V1 = Riadi City).
- `App\Support\Scopes\RelaisScope` + trait `BelongsToRelais` : filtrage
  automatique des requêtes par relais, et remplissage auto de `relais_id`.
- Pour requêter tous les relais (admin central) :
  `Model::withoutGlobalScope(RelaisScope::class)`.

> Tous les montants sont stockés en **entiers (centimes)** — voir `App\Support\Money`.

## Installation (dév local)

```bash
composer install
cp .env.example .env
php artisan key:generate
# DB_CONNECTION=sqlite pour aller vite, puis :
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Admin de démonstration (créé par le seeder) :
**admin@naaqati.test** / **password** — à changer immédiatement.
Panel admin : `/admin`.

## Déploiement o2switch (mutualisé) — Phase 1

1. PHP **8.2/8.3** + extensions `pdo_mysql, mbstring, gd, intl, zip, bcmath`.
2. Pointer le domaine `naaqati.mon-agenceweb.fr` vers le dossier **`public/`**.
3. Créer la base MySQL et renseigner `.env` (bloc MySQL).
4. `composer install --no-dev --optimize-autoloader`
5. `php artisan migrate --force && php artisan db:seed --force`
6. `php artisan storage:link`
7. `php artisan config:cache && php artisan route:cache`
8. Cron (cPanel) pour le scheduler :
   `* * * * * php /chemin/naaqati/artisan schedule:run >> /dev/null 2>&1`

> Sur mutualisé, les queues tournent en mode `database` via le cron. Le passage
> à un **VPS** (V2) apportera un worker `queue:work` persistant (supervisord),
> Redis et l'API WhatsApp Business.

## Services métier

- `StockService` — réservation / vente / libération de stock avec verrou
  transactionnel (anti-survente) + journal `stock_movements`.
- `OrderService` — machine à états des statuts + impact stock + historique.
- `PickupService` — créneaux de retrait disponibles (max 7 jours, blocages, capacité).
- `WhatsappService` — V1 : liens `wa.me` pré-remplis ; V2 : API Cloud.

## Roadmap

- **V1** : catalogue, panier, commande, retrait, admin Filament, WhatsApp manuel.
- **V2** : WhatsApp API, suivi J+15/30/45, feedback, coffrets, dashboard. → VPS.
- **V3** : multi-relais réel, rôles, multi-pays/devises, stats par relais.
