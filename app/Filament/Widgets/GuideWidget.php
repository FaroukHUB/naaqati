<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use Filament\Widgets\Widget;

class GuideWidget extends Widget
{
    protected static string $view = 'filament.widgets.guide';

    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        // Produits actifs mais sans stock disponible => invisibles ou épuisés.
        $produitsInvisibles = Product::where('actif', true)
            ->whereDoesntHave('inventories', fn ($q) => $q->where('actif', true)->where('stock_disponible', '>', 0))
            ->count();

        // Catégories actives sans aucun produit.
        $categoriesVides = Category::where('actif', true)
            ->whereDoesntHave('products')
            ->count();

        return [
            'produitsInvisibles' => $produitsInvisibles,
            'categoriesVides' => $categoriesVides,
        ];
    }
}
