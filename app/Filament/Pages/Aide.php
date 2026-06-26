<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Aide extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'Aide';

    protected static ?int $navigationSort = -50;

    protected static ?string $title = 'Aide & guide';

    protected static string $view = 'filament.pages.aide';
}
