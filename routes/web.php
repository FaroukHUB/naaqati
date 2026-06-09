<?php

use App\Http\Controllers\Admin\OrderWhatsappController;
use App\Livewire\Account\Dashboard as AccountDashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Storefront\CartPage;
use App\Livewire\Storefront\Catalog;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\ProductDetail;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// --- Boutique cliente (tunnel de commande) ---
Route::get('/', Catalog::class)->name('shop.catalog');
Route::get('/produit/{slug}', ProductDetail::class)->name('shop.product');
Route::get('/panier', CartPage::class)->name('shop.cart');
Route::get('/commande', Checkout::class)->name('shop.checkout');
Route::get('/assistante', fn () => view('shop.assistante'))->name('shop.assistante');

// --- Compte client ---
Route::get('/connexion', Login::class)->name('shop.login');
Route::get('/inscription', Register::class)->name('shop.register');
Route::post('/deconnexion', function () {
    Auth::guard('customer')->logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('shop.catalog');
})->name('shop.logout');
Route::get('/mon-compte', AccountDashboard::class)->middleware('auth:customer')->name('shop.account');
Route::get('/commande/{numero}/confirmation', function (string $numero) {
    $order = Order::where('numero', $numero)
        ->with(['customer', 'relais', 'packages.packaging', 'packages.items'])
        ->firstOrFail();

    return view('shop.confirmation', ['order' => $order]);
})->name('shop.confirmation');

// --- Génération du lien WhatsApp « commande prête » (réservé aux admins connectés) ---
Route::get('/admin/orders/{order}/whatsapp', OrderWhatsappController::class)
    ->middleware(['web', 'auth'])
    ->name('admin.orders.whatsapp');
