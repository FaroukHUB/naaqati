<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Filament\Resources\InventoryResource\RelationManagers;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'id')
                    ->required(),
                Forms\Components\Select::make('relais_id')
                    ->relationship('relais', 'id')
                    ->required(),
                Forms\Components\TextInput::make('stock_disponible')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('stock_reserve')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('stock_vendu')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('prix_override')
                    ->numeric(),
                Forms\Components\Toggle::make('actif')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('relais.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_disponible')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_reserve')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_vendu')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prix_override')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}
