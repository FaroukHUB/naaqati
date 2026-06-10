<?php

namespace App\Filament\Resources\ConcernResource\Pages;

use App\Filament\Resources\ConcernResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConcern extends EditRecord
{
    protected static string $resource = ConcernResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
