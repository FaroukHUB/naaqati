<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Vitrine';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Slide d\'accueil';

    protected static ?string $pluralModelLabel = 'Slides d\'accueil';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                ->label('Image de fond')
                ->collection('image')
                ->image()
                ->imageEditor()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('titre')
                ->label('Titre (optionnel)')
                ->maxLength(255),
            Forms\Components\TextInput::make('sous_titre')
                ->label('Sous-titre (optionnel)')
                ->maxLength(255),
            Forms\Components\TextInput::make('bouton_texte')
                ->label('Texte du bouton (optionnel)')
                ->maxLength(60),
            Forms\Components\TextInput::make('lien')
                ->label('Lien du bouton (optionnel)')
                ->placeholder('/?cat=1 ou https://…'),
            Forms\Components\TextInput::make('position')
                ->label('Ordre')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('actif')
                ->label('Actif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')->label('Image')->collection('image'),
                Tables\Columns\TextColumn::make('titre')->label('Titre')->placeholder('—'),
                Tables\Columns\IconColumn::make('actif')->label('Actif')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()->label('Modifier')])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->label('Supprimer')])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
