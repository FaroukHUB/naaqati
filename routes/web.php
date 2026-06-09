<?php

use App\Http\Controllers\Admin\OrderWhatsappController;
use App\Livewire\Storefront\CartPage;
use App\Livewire\Storefront\Catalog;
use App\Livewire\Storefront\Checkout;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// --- Boutique cliente (tunnel de commande) ---
Route::get('/', Catalog::class)->name('shop.catalog');
Route::get('/panier', CartPage::class)->name('shop.cart');
Route::get('/commande', Checkout::class)->name('shop.checkout');
Route::get('/commande/{numero}/confirmation', function (string $numero) {
    $order = Order::where('numero', $numero)->with(['customer', 'relais', 'items'])->firstOrFail();

    return view('shop.confirmation', ['order' => $order]);
})->name('shop.confirmation');

// --- Génération du lien WhatsApp « commande prête » (réservé aux admins connectés) ---
Route::get('/admin/orders/{order}/whatsapp', OrderWhatsappController::class)
    ->middleware(['web', 'auth'])
    ->name('admin.orders.whatsapp');
