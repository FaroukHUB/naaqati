<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'packages';

    protected static ?string $title = 'Paquets cadeaux';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->label('Paquet')
                    ->formatStateUsing(fn ($state) => 'N° ' . $state),
                Tables\Columns\TextColumn::make('packaging.nom')
                    ->label('Emballage')
                    ->badge()
                    ->placeholder('Sachet kraft'),
                Tables\Columns\TextColumn::make('nom_destinataire')
                    ->label('Pour')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('message_cadeau')
                    ->label('Message')
                    ->placeholder('—')
                    ->wrap(),
                Tables\Columns\TextColumn::make('frais_emballage')
                    ->label('Frais')
                    ->money('DZD', divideBy: 100),
            ])
            ->paginated(false);
    }
}
