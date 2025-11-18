<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        // Auth routes
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Products
        Route::get('/products/search', [ProductController::class, 'search']);
        Route::apiResource('products', ProductController::class);
        Route::post('/products/bulk-import', [ProductController::class, 'bulkImport'])
            ->middleware('role:admin,vendor');

        // Orders
        Route::apiResource('orders', OrderController::class);
        Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])
            ->middleware('role:admin,vendor');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->middleware('role:admin,vendor');

        // Inventory
        Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])
            ->middleware('role:admin,vendor');
        Route::apiResource('inventory', InventoryController::class)
            ->middleware('role:admin,vendor');
    });
});

