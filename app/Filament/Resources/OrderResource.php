<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\PackagesRelationManager;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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

    public static function getNavigationBadge(): ?string
    {
        $n = static::getModel()::where('statut', OrderStatus::Recue->value)->count();

        return $n > 0 ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

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
                    ->displayFormat('d/m/Y')
                    ->placeholder('À définir'),
                Forms\Components\TextInput::make('creneau_retrait')
                    ->label('Créneau / heure de retrait')
                    ->placeholder('À définir'),
                Forms\Components\TextInput::make('recuperateur')
                    ->label('Qui récupère')
                    ->placeholder('Non précisé'),
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
                    ->date('d/m/Y')
                    ->placeholder('À définir')
                    ->sortable(),
                Tables\Columns\TextColumn::make('creneau_retrait')
                    ->label('Créneau')
                    ->placeholder('À définir'),
                Tables\Columns\TextColumn::make('recuperateur')
                    ->label('Récupère')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('monnaie')
                    ->label('Monnaie')
                    ->badge()
                    ->color(fn (Order $record) => $record->a_l_appoint ? 'success' : 'warning')
                    ->getStateUsing(fn (Order $record) => $record->a_l_appoint
                        ? 'Appoint ✓'
                        : 'Rendre ' . number_format($record->monnaieARendre() / 100, 0, ',', ' ') . ' DA (paie ' . number_format(($record->paie_avec ?? 0) / 100, 0, ',', ' ') . ')'),
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
                Tables\Actions\Action::make('changerStatut')
                    ->label('Statut')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Order $record) => $record->statut->transitionsAutorisees() !== [])
                    ->form([
                        Forms\Components\Select::make('statut')
                            ->label('Nouveau statut')
                            ->options(fn (Order $record) => $record->statut->optionsSuivantes())
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Note (optionnel)')
                            ->rows(2),
                    ])
                    ->action(function (Order $record, array $data) {
                        try {
                            app(OrderService::class)->changerStatut(
                                $record,
                                OrderStatus::from($data['statut']),
                                $data['note'] ?? null,
                            );
                            Notification::make()
                                ->title('Statut mis à jour')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Action impossible')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->statut === OrderStatus::Prete)
                    ->url(fn (Order $record) => route('admin.orders.whatsapp', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()->label('Voir'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PackagesRelationManager::class,
            OrderItemsRelationManager::class,
        ];
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
