<?php

namespace App\Filament\Resources;

use App\Enums\PackagingType;
use App\Filament\Resources\PackagingResource\Pages;
use App\Models\Packaging;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PackagingResource extends Resource
{
    protected static ?string $model = Packaging::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Emballage';

    protected static ?string $pluralModelLabel = 'Emballages';

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->label('Nom de l\'emballage')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options(PackagingType::options())
                    ->required(),
                Forms\Components\TextInput::make('prix')
                    ->label('Prix')
                    ->numeric()
                    ->default(0)
                    ->suffix('DA')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state / 100 : 0)
                    ->dehydrateStateUsing(fn ($state) => (int) round(((float) $state) * 100)),
                Forms\Components\TextInput::make('couleur')
                    ->label('Couleur (optionnel)')
                    ->maxLength(40),
                Forms\Components\Toggle::make('actif')
                    ->label('Emballage actif')
                    ->default(true),
                Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                    ->label('Photo de l\'emballage')
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
                    ->circular(),
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (PackagingType $state) => $state->label()),
                Tables\Columns\TextColumn::make('prix')
                    ->label('Prix')
                    ->money('DZD', divideBy: 100)
                    ->sortable(),
                Tables\Columns\TextColumn::make('couleur')
                    ->label('Couleur')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
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
            'index' => Pages\ListPackagings::route('/'),
            'create' => Pages\CreatePackaging::route('/create'),
            'edit' => Pages\EditPackaging::route('/{record}/edit'),
        ];
    }
}
