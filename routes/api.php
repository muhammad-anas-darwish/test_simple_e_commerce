<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::apiResource('products', ProductController::class)->only(['index', 'show']);

Route::get('/cart/items', [CartItemController::class, 'index'])->name('cart.items');
Route::post('/cart/items', [CartItemController::class, 'updateCart'])->name('cart.update');
Route::apiResource('orders', OrderController::class)->only(['store', 'show']);
Route::get('/user/orders', [OrderController::class, 'getUserOrders'])->name('orders.getUserOrders');

Route::group(['middleware' => ['is_admin']], function () {
    Route::apiResource('products', ProductController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'update']);
});
