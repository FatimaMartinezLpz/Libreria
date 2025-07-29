<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
//  Rutas públicas (auth + libros visibles sin login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
//  Libros accesibles para usuarios no autenticados
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);
// En routes/api.php
Route::get('/books/search', [BookController::class, 'search'])->name('books.search');


// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/books/verify-stock', [BookController::class, 'verifyStock']);


    // Rutas de carrito para todos los usuarios autenticados
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartItemController::class, 'index']);
        Route::post('/', [CartItemController::class, 'store']);
        Route::put('/{id}', [CartItemController::class, 'update']);
        Route::put('/{id}/decrease', [CartItemController::class, 'decreaseQuantity']);
        Route::delete('/{id}', [CartItemController::class, 'destroy']);
    });
    // Dentro del grupo auth:sanctum
    Route::post('/orders', [OrderController::class, 'store']); // Esta línea
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Rutas para administradores y editores
    Route::middleware('role:admin,editor')->group(function () {
        Route::apiResource('books', BookController::class)->except(['index', 'show']);
    });

    // Rutas exclusivas para administradores
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    Route::middleware('auth:sanctum')->get('/test-token', function (Request $request) {
        return [
            'user_id' => $request->user()->id,
            'role' => $request->user()->role,
            'is_admin' => $request->user()->role === 'admin'
        ];
    });
});

