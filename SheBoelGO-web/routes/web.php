<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\SalesReportController;

Route::get('/', function () {
    return view('welcome');
});

//AUTH
Route::get('/auth/{mode?}', [AuthController::class, 'show'])->name('auth.page');
Route::post('/auth', [AuthController::class, 'submit'])->name('auth.submit');
// Setelah login
Route::get('/main', [MainController::class, 'main'])->name('main');
Route::get('/main_admin', fn() => view('admin.main'))->name('main_admin');

Route::get('/api/category/{category}', [MainController::class, 'getByCategory']);
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::post('/cart/add', [ItemController::class, 'addToCart'])->name('cart.add');

Route::get('/item/{itemId}', [ItemController::class, 'show'])->name('item.detail');

//CART
Route::get('/cart', [CartController::class, 'index'])->name('cart');

Route::post('/cart/cancel', [CartController::class, 'cancelOrder'])->name('cart.cancel');

Route::post('/cart/complete', [CartController::class, 'completeOrder'])->name('cart.complete');

Route::post('/cart/track-status', [CartController::class, 'getOrderStatus'])->name('cart.track');


Route::get('/rating/{itemId}', [RatingController::class, 'show'])->name('rating');
Route::post('/rating', [RatingController::class, 'store'])->name('rating.submit');

Route::prefix('admin/products')->name('admin.products.')->group(function () {
    Route::get('create', [AdminProductController::class, 'create'])->name('create');
    Route::post('/', [AdminProductController::class, 'store'])->name('store');
    Route::get('delete', [AdminProductController::class, 'delete'])->name('delete');
    Route::delete('{id}', [AdminProductController::class, 'destroy'])->name('destroy');
    Route::get('edit', [AdminProductController::class, 'edit'])->name('edit');
    Route::get('edit/{id}', [AdminProductController::class, 'editForm'])->name('editForm');
    Route::put('{id}', [AdminProductController::class, 'update'])->name('update');
});

Route::get('/sales-report', [SalesReportController::class, 'index'])->name('admin.sales_report');
Route::get('/sales-report/{collection}/{id}', [SalesReportController::class, 'detail'])->name('admin.sales_report.detail');
Route::put('/sales-report/{collection}/{id}', [SalesReportController::class, 'updateStatus'])->name('admin.sales_report.update_status');
Route::post('/sales-report/{from}/{to}/{id}/move', [SalesReportController::class, 'move'])->name('admin.sales_report.move');
