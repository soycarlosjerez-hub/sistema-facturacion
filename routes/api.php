<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BusinessTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::patch('categories/{category}/toggle-activa', [CategoryController::class, 'toggleActiva'])
        ->name('categories.toggle-activa');

    Route::post('categories/reorder', [CategoryController::class, 'reorder'])
        ->name('categories.reorder');

    Route::post('categories/{category}/type', [CategoryController::class, 'toggleType'])
        ->name('categories.toggle-type');

    Route::apiResource('business-types', BusinessTypeController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
});