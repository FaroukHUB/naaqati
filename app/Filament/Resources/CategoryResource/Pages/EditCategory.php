<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Category $record, Actions\DeleteAction $action) {
                    if ($record->products()->exists() || $record->children()->exists()) {
                        Notification::make()
                            ->title('Suppression impossible')
                            ->body('Cette catégorie contient des produits ou des sous-catégories. Déplacez-les (ou supprimez-les) d\'abord.')
                            ->danger()
                            ->persistent()
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
