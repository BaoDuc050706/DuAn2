<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;

Route::redirect('/', '/home');

Route::get('/home', function () {
    return view('home');
})->name('home');

// Checkout routes
Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [PaymentController::class, 'process'])->name('checkout.process');

// Product detail
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
