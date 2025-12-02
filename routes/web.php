<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ScrappedController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
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

Route::get('/', function () {
    return redirect()->route('login');
})->middleware('auth');


Route::get('/pay/{id}', [FrontController::class, 'payNow'])->name('pay');
Route::get('/invoice/{id}', [FrontController::class, 'invoice'])->name('invoice');
Route::post('pay.now', [StripeController::class, 'stripePost'])->name('pay.now');
Route::post('payment/save', [FrontController::class, 'paymentSave'])->name('payment.save');
Route::post('payment/authorize', [StripeController::class, 'paymentAuthorize'])->name('payment.authorize');
Route::get('success/{id}', [StripeController::class, 'successPayment'])->name('success.payment');
Route::get('declined/{id}', [StripeController::class, 'declinedPayment'])->name('declined.payment');
Route::get('export', [FrontController::class, 'export'])->name('export');

// Route::get('stripe', [StripeController::class, 'stripe']);
Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');
Route::post('process-payment', [StripeController::class, 'processPayment'])->name('process.payment');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
    Route::post('update/profile', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('update.profile');
    Route::resource('clients', ClientController::class);
    Route::resource('payment', PaymentController::class);
    Route::resource('brand', BrandsController::class);
    Route::resource('merchant', MerchantController::class);
    Route::get('show/response/{id}', [App\Http\Controllers\HomeController::class, 'showResponse'])->name('show.response');
    Route::get('payment/delete/{id}', [PaymentController::class, 'delete'])->name('payment.delete');
    Route::post('paid', [PaymentController::class, 'paid'])->name('payment.paid');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::get('/items/autocomplete', [PaymentController::class, 'autocomplete'])->name('items.autocomplete');
});

Auth::routes(['register' => false]);


