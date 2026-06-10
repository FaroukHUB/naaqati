<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Inventory;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersOverview extends BaseWidget
{
    protected static ?int $sort = -3;

    protected function getStats(): array
    {
        $aujourdhui = Order::whereDate('created_at', today())->count();

        $aPreparer = Order::whereIn('statut', [
            OrderStatus::Recue->value,
            OrderStatus::EnPreparation->value,
        ])->count();

        $pretes = Order::where('statut', OrderStatus::Prete->value)->count();

        $caJour = (int) Order::whereDate('created_at', today())
            ->where('statut', '!=', OrderStatus::Annulee->value)
            ->sum('total');

        $ruptures = Inventory::where('stock_disponible', '<=', 0)->where('actif', true)->count();

        return [
            Stat::make('Commandes du jour', $aujourdhui)
                ->description('Reçues aujourd\'hui')
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),
            Stat::make('À préparer', $aPreparer)
                ->description('Reçues + en préparation')
                ->icon('heroicon-o-clock')
                ->color($aPreparer > 0 ? 'warning' : 'gray'),
            Stat::make('Prêtes à récupérer', $pretes)
                ->icon('heroicon-o-check-badge')
                ->color($pretes > 0 ? 'info' : 'gray'),
            Stat::make('CA du jour', number_format($caJour / 100, 0, ',', ' ') . ' DA')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Ruptures de stock', $ruptures)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($ruptures > 0 ? 'danger' : 'gray'),
        ];
    }
}
