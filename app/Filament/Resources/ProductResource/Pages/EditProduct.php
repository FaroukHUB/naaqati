<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Modifications enregistrées ✅';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reappro')
                ->label('Stock')
                ->icon('heroicon-o-archive-box')
                ->color('warning')
                ->modalHeading('Réapprovisionner')
                ->modalSubmitActionLabel('Enregistrer')
                ->form([
                    Forms\Components\TextInput::make('stock_disponible')
                        ->label('Nouveau stock à Riadi City')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->default(fn () => ProductResource::stockActuel($this->record)),
                ])
                ->action(function (array $data) {
                    ProductResource::appliquerStock($this->record, (int) $data['stock_disponible']);
                    Notification::make()->title('Stock mis à jour ✅')->success()->send();
                }),
            Actions\Action::make('preview')
                ->label('Voir sur le site')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn (Product $record) => route('shop.product', $record->slug))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()->label('Supprimer'),
        ];
    }
}
