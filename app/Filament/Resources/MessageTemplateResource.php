<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageTemplateResource\Pages;
use App\Models\MessageTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Réglages';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Message WhatsApp';

    protected static ?string $pluralModelLabel = 'Messages WhatsApp';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nom')
                ->label('Nom du message')
                ->required(),
            Forms\Components\Textarea::make('corps')
                ->label('Texte du message')
                ->rows(10)
                ->required()
                ->columnSpanFull()
                ->helperText('Variables disponibles : {{nom}} {{numero}} {{relais}} {{adresse}} {{batiment}} {{code_portail}} {{code_porte}} {{code_ascenseur}} {{etage}} {{instructions}} {{date}} {{creneau}}'),
            Forms\Components\Toggle::make('actif')
                ->label('Actif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')->label('Message')->searchable(),
                Tables\Columns\TextColumn::make('cle')->label('Clé')->badge(),
                Tables\Columns\TextColumn::make('corps')->label('Aperçu')->limit(60)->wrap(),
                Tables\Columns\IconColumn::make('actif')->label('Actif')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()->label('Modifier')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageTemplates::route('/'),
            'create' => Pages\CreateMessageTemplate::route('/create'),
            'edit' => Pages\EditMessageTemplate::route('/{record}/edit'),
        ];
    }
}
