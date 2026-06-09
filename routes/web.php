<?php

use App\Http\Controllers\Admin\OrderWhatsappController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Génération du lien WhatsApp « commande prête » (réservé aux admins connectés).
Route::get('/admin/orders/{order}/whatsapp', OrderWhatsappController::class)
    ->middleware(['web', 'auth'])
    ->name('admin.orders.whatsapp');
