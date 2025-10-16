<?php

use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
<<<<<<<<< Temporary merge branch 1
=========
=======
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;

>>>>>>> 577f0083dab6690302a81040931ef83fc8474bba
Route::redirect('/', '/home');

>>>>>>>>> Temporary merge branch 2
Route::get('/home', function () {
    return view('home');
})->name('home');

// Checkout routes (require login)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [PaymentController::class, 'process'])->name('checkout.process');
});

// Product detail
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

<<<<<<< HEAD
// Category page (placeholder)
Route::get('/category/{slug}', function (string $slug) {
    return response()->view('welcome', [ 'slug' => $slug ]);
})->name('category.show');
=======
// Auth - Login (demo)
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Auth - Register (demo)
Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.submit');

// Profile (require login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::delete('/cart/{index}', [CartController::class, 'remove'])->name('cart.remove');
Route::patch('/cart/{index}/inc', [CartController::class, 'increment'])->name('cart.inc');
Route::patch('/cart/{index}/dec', [CartController::class, 'decrement'])->name('cart.dec');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
>>>>>>> 577f0083dab6690302a81040931ef83fc8474bba
