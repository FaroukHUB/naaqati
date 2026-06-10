<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Alertes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationLabel = 'Alertes push';

    protected static ?string $navigationGroup = 'Réglages';

    protected static ?string $title = 'Notifications de commande';

    protected static string $view = 'filament.pages.alertes';

    public ?string $vapidPublicKey = null;

    public function mount(): void
    {
        $this->vapidPublicKey = config('services.webpush.public_key');
    }
}
