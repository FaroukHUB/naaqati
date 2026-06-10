<?php

namespace App\Filament\Resources\RelaisResource\Pages;

use App\Filament\Resources\RelaisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRelais extends ListRecords
{
    protected static string $resource = RelaisResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
