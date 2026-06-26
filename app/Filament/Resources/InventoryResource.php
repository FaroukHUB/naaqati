<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    // Le stock se gère directement depuis la fiche produit (bouton « Stock »).
    // Cet écran détaillé reste accessible mais masqué du menu pour simplifier.
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Stock';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Stock';

    protected static ?string $pluralModelLabel = 'Stocks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produit')
                    ->relationship('product', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('stock_disponible')
                    ->label('Stock disponible')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->helperText('Quantité vendable. Pour un ajustement précis, on branchera bientôt l\'action « Ajustement » (journal des mouvements).'),
                Forms\Components\TextInput::make('stock_reserve')
                    ->label('Stock réservé')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Géré automatiquement par les commandes.'),
                Forms\Components\TextInput::make('stock_vendu')
                    ->label('Stock vendu')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Géré automatiquement par les commandes.'),
                Forms\Components\TextInput::make('prix_override')
                    ->label('Prix spécifique à ce relais (optionnel)')
                    ->numeric()
                    ->suffix('DA')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state / 100 : null)
                    ->dehydrateStateUsing(fn ($state) => $state !== null && $state !== '' ? (int) round(((float) $state) * 100) : null),
                Forms\Components\Toggle::make('actif')
                    ->label('Actif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.nom')
                    ->label('Produit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('relais.nom')
                    ->label('Point relais')
                    ->badge(),
                Tables\Columns\TextColumn::make('stock_disponible')
                    ->label('Disponible')
                    ->badge()
                    ->color(fn (int $state) => $state > 0 ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_reserve')
                    ->label('Réservé')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_vendu')
                    ->label('Vendu')
                    ->sortable(),
                Tables\Columns\IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('rupture')
                    ->label('En rupture')
                    ->query(fn ($query) => $query->where('stock_disponible', '<=', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Modifier'),
            ])
            ->defaultSort('product_id');
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
