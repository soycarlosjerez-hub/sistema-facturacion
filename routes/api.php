<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BusinessTypeController;
use App\Http\Controllers\Api\BusinessInstanceController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\CajaController;
use App\Http\Controllers\Api\MesaController;
use App\Http\Controllers\Api\AlmacenController;
use App\Http\Controllers\Api\TipoVentaController;
use App\Http\Controllers\Api\TipoCompraController;
use App\Http\Controllers\Api\CotizacionController;
use App\Http\Controllers\Api\DevolucionController;
use App\Http\Controllers\Api\AlquilerController;
use App\Http\Controllers\Api\ConduceController;
use App\Http\Controllers\Api\LavaderoController;
use App\Http\Controllers\Api\ReservacionController;
use App\Http\Controllers\Api\ListaPrecioController;
use App\Http\Controllers\Api\BackupController;
use App\Http\Controllers\Api\PaymentProcessorController;
use App\Http\Controllers\Api\ImpresoraController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\NcfSequenceController;
use App\Http\Controllers\Api\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    // Categories
    Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::patch('categories/{category}/toggle-activa', [CategoryController::class, 'toggleActiva'])
        ->name('categories.toggle-activa');

    Route::post('categories/reorder', [CategoryController::class, 'reorder'])
        ->name('categories.reorder');

    Route::post('categories/{category}/type', [CategoryController::class, 'toggleType'])
        ->name('categories.toggle-type');

    // Business Types
    Route::apiResource('business-types', BusinessTypeController::class)
        ->names('api.business-types');

    // Business Instances
    Route::apiResource('instances', BusinessInstanceController::class)
        ->except(['edit', 'create']);

    Route::patch('instances/{businessInstance}/toggle-module', [BusinessInstanceController::class, 'toggleModule'])
        ->name('instances.toggle-module');

    // Users
    Route::get('users/me', [UserController::class, 'me'])
        ->name('users.me');

    Route::apiResource('users', UserController::class)
        ->except(['edit', 'create']);

    // Products
    Route::apiResource('products', ProductoController::class)
        ->except(['edit', 'create']);

    // Sales
    Route::apiResource('sales', VentaController::class)
        ->except(['edit', 'create']);

    Route::get('sales/resumen', [VentaController::class, 'resumen'])
        ->name('sales.resumen');

    // Customers
    Route::apiResource('customers', ClienteController::class)
        ->except(['edit', 'create']);

    // Purchases
    Route::apiResource('purchases', CompraController::class)
        ->except(['edit', 'create']);

    // Suppliers
    Route::apiResource('suppliers', ProveedorController::class)
        ->except(['edit', 'create']);

    // Branches
    Route::apiResource('branches', SucursalController::class)
        ->except(['edit', 'create']);

    // Cash Registers
    Route::apiResource('cash-registers', CajaController::class)
        ->except(['edit', 'create']);

    // Tables
    Route::apiResource('tables', MesaController::class)
        ->except(['edit', 'create']);

    // Warehouses
    Route::apiResource('warehouses', AlmacenController::class)
        ->except(['edit', 'create']);

    // Sale Types
    Route::apiResource('sale-types', TipoVentaController::class)
        ->except(['edit', 'create']);

    // Purchase Types
    Route::apiResource('purchase-types', TipoCompraController::class)
        ->except(['edit', 'create']);

    // Quotes
    Route::apiResource('quotes', CotizacionController::class)
        ->except(['edit', 'create']);

    // Returns
    Route::apiResource('returns', DevolucionController::class)
        ->except(['edit', 'create']);

    // Rentals
    Route::apiResource('rentals', AlquilerController::class)
        ->except(['edit', 'create']);

    // Delivery
    Route::apiResource('delivery', ConduceController::class)
        ->except(['edit', 'create']);

    // Laundry
    Route::apiResource('laundry', LavaderoController::class)
        ->except(['edit', 'create']);

    // Reservations
    Route::apiResource('reservations', ReservacionController::class)
        ->except(['edit', 'create']);

    // Price Lists
    Route::apiResource('price-lists', ListaPrecioController::class)
        ->except(['edit', 'create']);

    // Backups
    Route::apiResource('backups', BackupController::class)
        ->except(['edit', 'create']);

    // Payment Processors
    Route::apiResource('payment-processors', PaymentProcessorController::class)
        ->except(['edit', 'create']);

    // Printers
    Route::apiResource('printers', ImpresoraController::class)
        ->except(['edit', 'create']);

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index');

    // NCF Sequences
    Route::apiResource('ncf-sequences', NcfSequenceController::class)
        ->except(['edit', 'create']);

    // System Settings
    Route::apiResource('settings', SystemSettingController::class)
        ->except(['edit', 'create']);

    // Reports
    Route::get('reports/dashboard', [ReportController::class, 'dashboard'])
        ->name('reports.dashboard');

    Route::get('reports/top-products', [ReportController::class, 'topProductos'])
        ->name('reports.top-products');

    Route::get('reports/top-customers', [ReportController::class, 'topClientes'])
        ->name('reports.top-customers');

    Route::get('reports/inventory-low-stock', [ReportController::class, 'inventarioBajoStock'])
        ->name('reports.inventory-low-stock');
});
