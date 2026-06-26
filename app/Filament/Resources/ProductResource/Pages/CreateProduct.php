<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Inventory;
use App\Support\CurrentRelais;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected int $stockInitial = 0;

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->stockInitial > 0
            ? 'Produit créé et publié sur la boutique ✅'
            : 'Produit créé ✅ — ajoutez-lui du stock (bouton « Stock ») pour qu\'il apparaisse';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->stockInitial = (int) ($data['stock_initial'] ?? 0);
        unset($data['stock_initial']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $relaisId = app(CurrentRelais::class)->id();

        Inventory::firstOrCreate(
            ['product_id' => $this->record->id, 'relais_id' => $relaisId],
            ['stock_disponible' => $this->stockInitial, 'actif' => true],
        );
    }
}
