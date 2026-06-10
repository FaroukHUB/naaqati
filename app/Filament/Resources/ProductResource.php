<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Produit';

    protected static ?string $pluralModelLabel = 'Produits';

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->label('Nom du produit')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('prix_base')
                    ->label('Prix')
                    ->numeric()
                    ->required()
                    ->suffix('DA')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state / 100 : null)
                    ->dehydrateStateUsing(fn ($state) => (int) round(((float) $state) * 100)),
                Forms\Components\TextInput::make('poids_grammes')
                    ->label('Poids (grammes)')
                    ->numeric()
                    ->suffix('g'),
                Forms\Components\TextInput::make('desc_courte')
                    ->label('Description courte')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('desc_longue')
                    ->label('Description longue')
                    ->rows(5)
                    ->columnSpanFull(),
                Forms\Components\Select::make('concerns')
                    ->label('Besoins (recherche par besoin)')
                    ->relationship('concerns', 'nom')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('actif')
                    ->label('Produit actif')
                    ->default(true),
                Forms\Components\Toggle::make('en_avant')
                    ->label('Mettre en avant (Notre sélection du moment)')
                    ->helperText('Apparaît dans la sélection en page d\'accueil.'),
                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->label('Photos du produit')
                    ->collection('images')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('images')
                    ->label('Photo')
                    ->collection('images')
                    ->limit(1)
                    ->circular(),
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.nom')
                    ->label('Catégorie')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prix_base')
                    ->label('Prix')
                    ->money('DZD', divideBy: 100)
                    ->sortable(),
                Tables\Columns\IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('en_avant')
                    ->label('En avant'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'nom'),
                Tables\Filters\TernaryFilter::make('actif')
                    ->label('Actif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Modifier'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ])
            ->defaultSort('nom');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
