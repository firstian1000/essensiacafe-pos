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
use App\Http\Controllers\StockController;
use App\Http\Controllers\ExpenseController;

Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminLoginController::class, 'login']);
Route::get('/csrf-token', fn () => response()->json(['token' => csrf_token()]))
    ->name('csrf.token');
Route::get('/login/google', [AdminLoginController::class, 'redirectToGoogle'])->middleware('guest.role')->name('login.google');
Route::get('/login/google/callback', [AdminLoginController::class, 'handleGoogleCallback'])->middleware('guest.role')->name('login.google.callback');
Route::get('/kasir/login', [AdminLoginController::class, 'showCashierLoginForm'])->name('cashier.login.form');
Route::post('/kasir/login', [AdminLoginController::class, 'cashierLogin'])->name('cashier.login');
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
Route::get('/logout', [AdminLoginController::class, 'logout'])->name('logout.get');

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin,cashier')->get('/', function () {
    if (auth('admin')->check()) {
        auth()->shouldUse('admin');

        return app(DashboardController::class)->index(request());
    }

    auth()->shouldUse('cashier');

    return redirect()->route('cashier.index');
})->name('dashboard');

Route::middleware(['auth:admin', 'role:admin'])->group(function () {
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    Route::resource('stocks', StockController::class)
        ->except(['show'])
        ->parameters(['stocks' => 'stock']);
    Route::resource('expenses', ExpenseController::class)->except(['show']);
    Route::get('/settings', [SettingController::class, 'index'])
        ->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])
        ->name('settings.store');
});

Route::middleware(['auth:admin,cashier', 'role:admin,cashier'])->group(function () {
    Route::resource('categories', CategoryController::class);

    Route::resource('menus', MenuController::class)->except(['show']);

    Route::put('/menus/{menu}/status', [MenuController::class, 'updateStatus'])
        ->name('menus.status');

    Route::put('/menus/{menu}/recommendation', [MenuController::class, 'updateRecommendation'])
        ->name('menus.recommendation');

    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
    Route::post('/cashier', [CashierController::class, 'store'])->name('cashier.store');
    Route::get('/cashier/receipt/{order}', [CashierController::class, 'receipt'])->name('cashier.receipt');

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

Route::post('/cart/restore', [CartController::class, 'restore'])
    ->name('cart.restore');

Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

Route::get('/cart/increase/{id}', [CartController::class, 'increase'])
    ->name('cart.increase');

Route::get('/cart/decrease/{id}', [CartController::class, 'decrease'])
    ->name('cart.decrease');

Route::patch('/cart/options/{id}', [CartController::class, 'updateOptions'])
    ->name('cart.options');

Route::patch('/cart/service-type', [CartController::class, 'updateServiceType'])
    ->name('cart.service-type');

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

Route::get('/order/success/{order}/status', [OrderSuccessController::class, 'status'])
    ->name('order.success.status');


/*
|--------------------------------------------------------------------------
| Midtrans
|--------------------------------------------------------------------------
*/

Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->name('midtrans.callback');
