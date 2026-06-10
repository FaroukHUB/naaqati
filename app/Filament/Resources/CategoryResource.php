<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Catégorie';

    protected static ?string $pluralModelLabel = 'Catégories';

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->label('Nom de la catégorie')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('parent_id')
                    ->label('Catégorie parente (optionnel)')
                    ->helperText('Laissez vide pour une catégorie principale. Choisissez une catégorie pour en faire une sous-catégorie.')
                    ->options(fn (?\App\Models\Category $record) => \App\Models\Category::query()
                        ->when($record, fn ($q) => $q->whereKeyNot($record->getKey()))
                        ->whereNull('parent_id')
                        ->orderBy('nom')
                        ->pluck('nom', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('position')
                    ->label('Ordre d\'affichage')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('actif')
                    ->label('Catégorie active')
                    ->default(true),
                Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                    ->label('Photo de la catégorie')
                    ->collection('image')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label('Photo')
                    ->collection('image')
                    ->square(),
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.nom')
                    ->label('Catégorie parente')
                    ->badge()
                    ->placeholder('Catégorie principale'),
                Tables\Columns\TextColumn::make('children_count')
                    ->label('Sous-catégories')
                    ->counts('children')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('position')
                    ->label('Ordre')
                    ->sortable(),
                Tables\Columns\IconColumn::make('actif')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Catégorie parente')
                    ->relationship('parent', 'nom'),
                Tables\Filters\TernaryFilter::make('actif')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Modifier'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ])
            ->defaultSort('position');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
