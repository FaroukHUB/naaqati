<?php

namespace App\Filament\Resources\RelaisResource\Pages;

use App\Filament\Resources\RelaisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRelais extends EditRecord
{
    protected static string $resource = RelaisResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
