<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RelaisResource\Pages;
use App\Models\Relais;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RelaisResource extends Resource
{
    protected static ?string $model = Relais::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Réglages';

    protected static ?int $navigationSort = 0;

    protected static ?string $modelLabel = 'Point relais';

    protected static ?string $pluralModelLabel = 'Points relais';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nom')
                ->label('Nom du point relais')
                ->required(),
            Forms\Components\TextInput::make('telephone')
                ->label('Téléphone du relais')
                ->tel(),
            Forms\Components\TextInput::make('adresse')
                ->label('Adresse')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('batiment')
                ->label('Bâtiment / Résidence'),
            Forms\Components\TextInput::make('code_portail')
                ->label('Code portail / Digicode'),
            Forms\Components\TextInput::make('code_porte')
                ->label('Code porte d\'entrée'),
            Forms\Components\TextInput::make('code_ascenseur')
                ->label('Code ascenseur'),
            Forms\Components\TextInput::make('etage')
                ->label('Étage'),
            Forms\Components\Textarea::make('instructions_acces')
                ->label('Instructions d\'accès')
                ->placeholder("Ex : Ascenseur jusqu'au 3e, interphone n°4, 2e digicode B1234, porte au fond du couloir…")
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Toggle::make('actif')
                ->label('Actif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('adresse')->label('Adresse')->placeholder('—')->wrap(),
                Tables\Columns\TextColumn::make('code_portail')->label('Code portail')->placeholder('—'),
                Tables\Columns\IconColumn::make('actif')->label('Actif')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()->label('Modifier')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRelais::route('/'),
            'create' => Pages\CreateRelais::route('/create'),
            'edit' => Pages\EditRelais::route('/{record}/edit'),
        ];
    }
}
