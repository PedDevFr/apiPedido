<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Sistema de Gestión de Pedidos (Mini ERP)
|--------------------------------------------------------------------------
*/

// Rutas públicas (autenticación)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // CRUD Clientes (admin y vendedor)
    Route::middleware('role:admin,vendedor')->apiResource('clients', ClientController::class);

    // CRUD Productos (admin y vendedor)
    Route::middleware('role:admin,vendedor')->apiResource('products', ProductController::class);

    // CRUD Pedidos (admin y vendedor)
    Route::middleware('role:admin,vendedor')->apiResource('orders', OrderController::class);
});
