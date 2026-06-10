<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Vitrine';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Avis';

    protected static ?string $pluralModelLabel = 'Avis (accueil)';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nom')
                ->label('Nom de la cliente')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('texte')
                ->label('Avis')
                ->required()
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Select::make('note')
                ->label('Note (étoiles)')
                ->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★'])
                ->default(5),
            Forms\Components\TextInput::make('position')
                ->label('Ordre')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('actif')
                ->label('Affiché')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\TextColumn::make('nom')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('texte')->label('Avis')->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('note')->label('Note')->formatStateUsing(fn ($state) => str_repeat('★', (int) $state)),
                Tables\Columns\IconColumn::make('actif')->label('Affiché')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()->label('Modifier')])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()->label('Supprimer')])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
