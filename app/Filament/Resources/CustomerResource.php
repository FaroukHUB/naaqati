<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $recordTitleAttribute = 'nom';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->label('Nom complet')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telephone')
                    ->label('Téléphone')
                    ->tel()
                    ->required()
                    ->maxLength(30),
                Forms\Components\TextInput::make('email')
                    ->label('Email (optionnel)')
                    ->email()
                    ->maxLength(190),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes internes')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nb_commandes')
                    ->label('Commandes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_depense')
                    ->label('Total dépensé')
                    ->money('DZD', divideBy: 100)
                    ->sortable(),
                Tables\Columns\TextColumn::make('derniere_commande_at')
                    ->label('Dernière commande')
                    ->dateTime('d/m/Y')
                    ->placeholder('—')
                    ->sortable(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
