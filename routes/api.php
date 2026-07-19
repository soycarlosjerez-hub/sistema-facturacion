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
use App\Http\Controllers\Api\Auth\ClienteAuthController;
use App\Http\Controllers\Api\ClientePedidoController;
use App\Http\Controllers\Api\OrdenController;
use App\Http\Controllers\Api\TerminalController;
use App\Http\Controllers\Api\SystemSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api-auth', 'tenant', 'api.request.logger'])->group(function () {
    // Categories
    Route::apiResource('categories', CategoryController::class)
        ->names('api.categories')
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::patch('categories/{category}/toggle-activa', [CategoryController::class, 'toggleActiva'])
        ->name('api.categories.toggle-activa');

    Route::post('categories/reorder', [CategoryController::class, 'reorder'])
        ->name('api.categories.reorder');

    Route::post('categories/{category}/type', [CategoryController::class, 'toggleType'])
        ->name('api.categories.toggle-type');

    // Business Types
    Route::apiResource('business-types', BusinessTypeController::class)
        ->names('api.business-types');

    // Business Instances
    Route::apiResource('instances', BusinessInstanceController::class)
        ->names('api.instances')
        ->except(['edit', 'create']);

    Route::patch('instances/{businessInstance}/toggle-module', [BusinessInstanceController::class, 'toggleModule'])
        ->name('api.instances.toggle-module');

    // Users
    Route::get('users/me', [UserController::class, 'me'])
        ->name('api.users.me');

    Route::apiResource('users', UserController::class)
        ->names('api.users')
        ->except(['edit', 'create']);

    // Products
    Route::apiResource('products', ProductoController::class)
        ->names('api.products')
        ->except(['edit', 'create']);

    // Cliente Auth (público)
    Route::prefix('auth')->name('api.auth.')->group(function () {
        Route::post('cliente/register', [ClienteAuthController::class, 'register'])->name('cliente.register');
        Route::post('cliente/login', [ClienteAuthController::class, 'login'])->name('cliente.login');
        Route::post('cliente/forgot-password', [ClienteAuthController::class, 'forgotPassword'])->name('cliente.forgot-password');
        Route::post('cliente/reset-password', [ClienteAuthController::class, 'resetPassword'])->name('cliente.reset-password');
        Route::post('cliente/resend-verification', [ClienteAuthController::class, 'resendVerification'])->name('cliente.resend-verification');
        Route::get('cliente/verify-email/{id}/{hash}', [ClienteAuthController::class, 'verifyEmail'])->name('cliente.verify-email');
    });

    // Cliente Authenticated Routes
    Route::prefix('cliente')->middleware('auth.cliente')->name('api.cliente.')->group(function () {
        Route::post('logout', [ClienteAuthController::class, 'logout'])->name('logout');
        Route::get('me', [ClienteAuthController::class, 'me'])->name('me');
        Route::put('profile', [ClienteAuthController::class, 'updateProfile'])->name('profile.update');
        Route::post('change-password', [ClienteAuthController::class, 'changePassword'])->name('change-password');
        Route::get('pedidos', [ClientePedidoController::class, 'index'])->name('pedidos.index');
        Route::get('pedidos/{id}', [ClientePedidoController::class, 'show'])->name('pedidos.show');
    });

    // Sales
    Route::apiResource('sales', VentaController::class)
        ->names('api.sales')
        ->except(['edit', 'create']);

    Route::get('sales/resumen', [VentaController::class, 'resumen'])
        ->name('api.sales.resumen');

    // Customers
    Route::apiResource('customers', ClienteController::class)
        ->names('api.customers')
        ->except(['edit', 'create']);

    // Purchases
    Route::apiResource('purchases', CompraController::class)
        ->names('api.purchases')
        ->except(['edit', 'create']);

    // Suppliers
    Route::apiResource('suppliers', ProveedorController::class)
        ->names('api.suppliers')
        ->except(['edit', 'create']);

    // Branches
    Route::apiResource('branches', SucursalController::class)
        ->names('api.branches')
        ->except(['edit', 'create']);

    // Cash Registers
    Route::apiResource('cash-registers', CajaController::class)
        ->names('api.cash-registers')
        ->except(['edit', 'create']);

    // Tables
    Route::apiResource('tables', MesaController::class)
        ->names('api.tables')
        ->except(['edit', 'create']);

    // Warehouses
    Route::apiResource('warehouses', AlmacenController::class)
        ->names('api.warehouses')
        ->except(['edit', 'create']);

    // Sale Types
    Route::apiResource('sale-types', TipoVentaController::class)
        ->names('api.sale-types')
        ->except(['edit', 'create']);

    // Purchase Types
    Route::apiResource('purchase-types', TipoCompraController::class)
        ->names('api.purchase-types')
        ->except(['edit', 'create']);

    // Quotes
    Route::apiResource('quotes', CotizacionController::class)
        ->names('api.quotes')
        ->except(['edit', 'create']);

    // Returns
    Route::apiResource('returns', DevolucionController::class)
        ->names('api.returns')
        ->except(['edit', 'create']);

    // Rentals
    Route::apiResource('rentals', AlquilerController::class)
        ->names('api.rentals')
        ->except(['edit', 'create']);

    // Delivery
    Route::apiResource('delivery', ConduceController::class)
        ->names('api.delivery')
        ->except(['edit', 'create']);

    // Laundry
    Route::apiResource('laundry', LavaderoController::class)
        ->names('api.laundry')
        ->except(['edit', 'create']);

    // Reservations
    Route::apiResource('reservations', ReservacionController::class)
        ->names('api.reservations')
        ->except(['edit', 'create']);

    // Price Lists
    Route::apiResource('price-lists', ListaPrecioController::class)
        ->names('api.price-lists')
        ->except(['edit', 'create']);

    // Backups
    Route::apiResource('backups', BackupController::class)
        ->names('api.backups')
        ->except(['edit', 'create']);

    // Payment Processors
    Route::apiResource('payment-processors', PaymentProcessorController::class)
        ->names('api.payment-processors')
        ->except(['edit', 'create']);

    // Printers
    Route::apiResource('printers', ImpresoraController::class)
        ->names('api.printers')
        ->except(['edit', 'create']);

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])
        ->name('api.audit-logs.index');

    // NCF Sequences
    Route::apiResource('ncf-sequences', NcfSequenceController::class)
        ->names('api.ncf-sequences')
        ->except(['edit', 'create']);

    // System Settings
    Route::apiResource('settings', SystemSettingController::class)
        ->names('api.settings')
        ->except(['edit', 'create']);

    // Orders
    Route::get('orders', [OrdenController::class, 'index'])
        ->name('api.orders.index')
        ->middleware('permission:ordenes.view');

    Route::post('orders', [OrdenController::class, 'store'])
        ->name('api.orders.store')
        ->middleware('permission:ordenes.create');

    Route::get('orders/{orden}', [OrdenController::class, 'show'])
        ->name('api.orders.show')
        ->middleware('permission:ordenes.view');

    Route::match(['put', 'patch'], 'orders/{orden}', [OrdenController::class, 'update'])
        ->name('api.orders.update')
        ->middleware('permission:ordenes.update');

    Route::delete('orders/{orden}', [OrdenController::class, 'destroy'])
        ->name('api.orders.destroy')
        ->middleware('permission:ordenes.cancel');

    Route::post('orders/{orden}/pay', [\App\Http\Controllers\Api\OrdenPaymentController::class, 'process'])
        ->name('api.orders.pay')
        ->middleware('permission:ordenes.pay');

    Route::delete('orders/{orden}/details/{detalle}', [\App\Http\Controllers\Api\OrdenDetailController::class, 'destroy'])
        ->name('api.orders.details.destroy')
        ->middleware('permission:ordenes.create');

    Route::patch('orders/{orden}/details/{detalle}', [\App\Http\Controllers\Api\OrdenDetailController::class, 'update'])
        ->name('api.orders.details.update')
        ->middleware('permission:ordenes.create');

    Route::post('orders/{orden}/details', [\App\Http\Controllers\Api\OrdenDetailController::class, 'store'])
        ->name('api.orders.details.store')
        ->middleware('permission:ordenes.create');

    // KDS
    Route::get('kds/orders', [\App\Http\Controllers\Api\KitchenDisplayController::class, 'index'])
        ->name('api.kds.orders');

    Route::patch('kds/orders/{detalle}/status', [\App\Http\Controllers\Api\KitchenDisplayController::class, 'updateStatus'])
        ->name('api.kds.orders.update-status');

    // Terminals
    Route::apiResource('terminals', TerminalController::class)
        ->names('api.terminals')
        ->except(['edit', 'create']);

    // Reports
    Route::get('reports/dashboard', [ReportController::class, 'dashboard'])
        ->name('api.reports.dashboard');

    Route::get('reports/top-products', [ReportController::class, 'topProductos'])
        ->name('api.reports.top-products');

    Route::get('reports/top-customers', [ReportController::class, 'topClientes'])
        ->name('api.reports.top-customers');

    Route::get('reports/inventory-low-stock', [ReportController::class, 'inventarioBajoStock'])
        ->name('api.reports.inventory-low-stock');
});
