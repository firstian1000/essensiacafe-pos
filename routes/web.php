<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CafeTableController;
use App\Http\Controllers\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderSuccessController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\SettingController;

Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [AdminLoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
Route::get('/logout', [AdminLoginController::class, 'logout'])->name('logout.get');

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');

    Route::resource('categories', CategoryController::class);

    Route::resource('menus', MenuController::class);

    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/cashier', [CashierController::class, 'store'])->name('cashier.store');
    Route::get('/cashier/receipt/{order}', [CashierController::class, 'receipt'])->name('cashier.receipt');

    Route::put('/menus/{menu}/status', [MenuController::class, 'updateStatus'])
        ->name('menus.status');

    Route::put('/menus/{menu}/recommendation', [MenuController::class, 'updateRecommendation'])
        ->name('menus.recommendation');

    Route::resource('tables', CafeTableController::class);

    Route::get('/tables/{table}/download', [QrCodeController::class, 'download'])
        ->name('tables.download');

    Route::get('/tables/print/all', [QrCodeController::class, 'printAll'])
        ->name('tables.print.all');

    Route::get('/orders/check-new', [OrderController::class, 'checkNew'])->name('orders.checkNew');
    Route::resource('orders', OrderController::class);

    Route::get('/orders/{order}/process', [OrderController::class, 'process'])
        ->name('orders.process');

    Route::get('/orders/{order}/unprocess', [OrderController::class, 'unprocess'])
        ->name('orders.unprocess');

    Route::get('/orders/{order}/complete', [OrderController::class, 'complete'])
        ->name('orders.complete');

    Route::get('/orders/{order}/cancel', [OrderController::class, 'cancel'])
        ->name('orders.cancel');

    Route::get('/orders/{order}/paid', [OrderController::class, 'paid'])
        ->name('orders.paid');

    Route::get('/payments', [PaymentController::class, 'index'])
        ->name('payments.index');

    Route::get('/payments/{order}/receipt', [PaymentController::class, 'receipt'])
        ->name('payments.receipt');

    Route::get('/payments/{order}', [PaymentController::class, 'show'])
        ->name('payments.show');

    Route::get('/settings', [SettingController::class, 'index'])
        ->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])
        ->name('settings.store');
});

/*
|--------------------------------------------------------------------------
| Customer Menu
|--------------------------------------------------------------------------
*/

Route::get('/order/{token}', [CustomerMenuController::class, 'index'])
    ->name('customer.menu');

/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/

Route::post('/cart/add', [CartController::class, 'add'])
    ->name('cart.add');

Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::get('/cart/increase/{id}', [CartController::class, 'increase'])
    ->name('cart.increase');

Route::get('/cart/decrease/{id}', [CartController::class, 'decrease'])
    ->name('cart.decrease');

Route::get('/cart/remove/{id}', [CartController::class, 'remove'])
    ->name('cart.remove');

Route::delete('/cart/clear', [CartController::class, 'clear'])
    ->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index');

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->name('checkout.store');

Route::get('/order/success/{order}', [OrderSuccessController::class, 'index'])
    ->name('order.success');


/*
|--------------------------------------------------------------------------
| Midtrans
|--------------------------------------------------------------------------
*/

Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->name('midtrans.callback');

