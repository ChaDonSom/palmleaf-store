<?php

use App\Livewire\AboutPage;
use App\Livewire\CheckoutPage;
use App\Livewire\CheckoutSuccessPage;
use App\Livewire\CollectionPage;
use App\Livewire\ContactPage;
use App\Livewire\CookiesPage;
use App\Livewire\FaqPage;
use App\Livewire\Home;
use App\Livewire\ProductPage;
use App\Livewire\SearchPage;
use App\Livewire\ShippingReturnsPage;
use App\Livewire\SizeGuidePage;
use Lunar\Models\Address;
use Lunar\Models\Order;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', Home::class);

Route::get('/collections/{slug}', CollectionPage::class)->name('collection.view');

Route::get('/products/{slug}', ProductPage::class)->name('product.view');

Route::get('search', SearchPage::class)->name('search.view');

Route::get('checkout', CheckoutPage::class)->name('checkout.view');

Route::get('checkout/success', CheckoutSuccessPage::class)->name('checkout-success.view');

Route::get('about', AboutPage::class)->name('about.view');

Route::get('contact', ContactPage::class)->name('contact.view');

Route::get('shipping-returns', ShippingReturnsPage::class)->name('shipping-returns.view');

Route::get('size-guide', SizeGuidePage::class)->name('size-guide.view');

Route::get('faq', FaqPage::class)->name('faq.view');

Route::get('cookies', CookiesPage::class)->name('cookies.view');

// Use the session-based "web" guard for Jetstream / Fortify protected pages.
// "auth:sanctum" switches the default guard to Sanctum's RequestGuard which
// does not implement StatefulGuard (no viaRemember method), causing
// AuthenticateSession to call viaRemember on a RequestGuard and explode.
// Keeping just 'auth' (alias for the default session guard) avoids that.
Route::middleware([
    'auth', // was 'auth:sanctum' – switched to session guard to fix viaRemember error
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::get('/user/profile/addresses/create', function () {
        return view('profile.addresses.show', ['address' => null]);
    })->name('profile-create-address');
    Route::get('/user/profile/addresses/{address}', function (Address $address) {
        return view('profile.addresses.show', ['address' => $address]);
    })->name('profile-edit-address');
    Route::get('/user/orders', function () {
        return view('orders');
    })->name('orders');
    Route::get('/user/orders/{order}', function (Order $order) {
        return view('orders.show', ['order' => $order]);
    })->name('orders.show');
});
