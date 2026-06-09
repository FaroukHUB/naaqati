<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articles & paquets';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nom_snapshot')
            ->modifyQueryUsing(fn ($query) => $query->with('orderPackage.packaging'))
            ->groups([
                Group::make('order_package_id')
                    ->label('Paquet')
                    ->getTitleFromRecordUsing(fn ($record) => $record->orderPackage?->libelle() ?? 'Sachet kraft'),
            ])
            ->defaultGroup('order_package_id')
            ->columns([
                Tables\Columns\TextColumn::make('nom_snapshot')
                    ->label('Produit'),
                Tables\Columns\TextColumn::make('quantite')
                    ->label('Qté'),
                Tables\Columns\TextColumn::make('prix_unitaire')
                    ->label('Prix unitaire')
                    ->money('DZD', divideBy: 100),
                Tables\Columns\TextColumn::make('total_ligne')
                    ->label('Total')
                    ->money('DZD', divideBy: 100),
            ])
            ->paginated(false);
    }
}
