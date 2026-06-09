<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Commande';

    protected static ?string $pluralModelLabel = 'Commandes';

    protected static ?string $recordTitleAttribute = 'numero';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero')
                    ->label('N° de commande')
                    ->disabled(),
                Forms\Components\Select::make('customer_id')
                    ->label('Cliente')
                    ->relationship('customer', 'nom')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('statut')
                    ->label('Statut')
                    ->options(OrderStatus::options())
                    ->required()
                    ->helperText('Le changement de statut avec impact sur le stock et l\'envoi WhatsApp sera bientôt automatisé.'),
                Forms\Components\DatePicker::make('date_retrait')
                    ->label('Date de retrait')
                    ->displayFormat('d/m/Y'),
                Forms\Components\TextInput::make('creneau_retrait')
                    ->label('Créneau de retrait'),
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->suffix('DA')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => $state !== null ? $state / 100 : null),
                Forms\Components\Textarea::make('commentaire')
                    ->label('Commentaire de la cliente')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('N°')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.nom')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label())
                    ->color(fn (OrderStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('DZD', divideBy: 100)
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_retrait')
                    ->label('Retrait')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('creneau_retrait')
                    ->label('Créneau'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Commandé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(OrderStatus::options()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Gérer'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
