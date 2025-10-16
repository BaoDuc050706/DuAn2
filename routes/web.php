<?php

use Illuminate\Support\Facades\Route;

<<<<<<<<< Temporary merge branch 1
=========
Route::redirect('/', '/home');

>>>>>>>>> Temporary merge branch 2
Route::get('/home', function () {
    return view('home');
})->name('home');

// Checkout routes
Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [PaymentController::class, 'process'])->name('checkout.process');

// Product detail
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// Category page (placeholder)
Route::get('/category/{slug}', function (string $slug) {
    return response()->view('welcome', [ 'slug' => $slug ]);
})->name('category.show');
