<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConcernResource\Pages;
use App\Models\Concern;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConcernResource extends Resource
{
    protected static ?string $model = Concern::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Vitrine';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Besoin';

    protected static ?string $pluralModelLabel = 'Besoins (recherche)';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nom')
                ->label('Nom du besoin')
                ->placeholder('Cheveux secs, Anti-chute, Peau sensible…')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('emoji')
                ->label('Emoji (optionnel)')
                ->placeholder('💧 🌿 ✨')
                ->maxLength(16),
            Forms\Components\TextInput::make('position')
                ->label('Ordre')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('actif')
                ->label('Actif')
                ->default(true),
            Forms\Components\Select::make('products')
                ->label('Produits associés')
                ->relationship('products', 'nom')
                ->multiple()
                ->searchable()
                ->preload()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\TextColumn::make('emoji')->label(''),
                Tables\Columns\TextColumn::make('nom')->label('Besoin')->searchable(),
                Tables\Columns\TextColumn::make('products_count')->label('Produits')->counts('products'),
                Tables\Columns\IconColumn::make('actif')->label('Actif')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()->label('Modifier')])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->label('Supprimer')])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConcerns::route('/'),
            'create' => Pages\CreateConcern::route('/create'),
            'edit' => Pages\EditConcern::route('/{record}/edit'),
        ];
    }
}
