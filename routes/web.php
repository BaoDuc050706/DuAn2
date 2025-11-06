<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; // ⚠️ THÊM DÒNG NÀY !!!

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;

// admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController;

Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Checkout routes (require login)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [PaymentController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [PaymentController::class, 'process'])->name('checkout.process');
    // Order history
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
});

// Public order lookup (no auth)
Route::get('/orders/lookup', [\App\Http\Controllers\OrderController::class, 'lookup'])->name('orders.lookup');

// Product detail
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
// Category page
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Auth - Login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'submit'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Auth - Register
Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'submit'])->name('register.submit');

// Profile (require login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{index}/inc', [CartController::class, 'increment'])->name('cart.inc');
Route::patch('/cart/{index}/dec', [CartController::class, 'decrement'])->name('cart.dec');
Route::delete('/cart/{index}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Clear / reset cart
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Debug route
Route::get('/debug-cart', function (Request $request) {
    dd([
        'session_id' => session()->getId(),
        'cart' => session()->get('cart'),
        'all_session_data' => session()->all()
    ]);
});

// Admin routes (require admin role)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::resource('/products', AdminProductController::class)->except(['show']);
});

// Test session route
Route::get('/test-session', function (Request $request) {
    dd([
        'cart' => $request->session()->get('cart'),
        'has_cart' => $request->session()->has('cart'),
    ]);
});
