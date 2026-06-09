<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Bouton WhatsApp bien visible quand la commande est prête.
            Actions\Action::make('whatsapp')
                ->label('Envoyer le WhatsApp « commande prête »')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->visible(fn (Order $record) => $record->statut === OrderStatus::Prete)
                ->url(fn (Order $record) => route('admin.orders.whatsapp', $record))
                ->openUrlInNewTab(),

            // Changer le statut (avec impact stock + historique).
            Actions\Action::make('changerStatut')
                ->label('Changer le statut')
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
                        Notification::make()->title('Statut mis à jour')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Action impossible')->body($e->getMessage())->danger()->send();
                    }
                }),

            Actions\DeleteAction::make()->label('Supprimer'),
        ];
    }
}
