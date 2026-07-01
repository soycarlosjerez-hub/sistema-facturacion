<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\NcfController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EcfController;
use App\Http\Controllers\SecuenciaEcfController;
use App\Http\Controllers\CertificadoDigitalController;
use App\Http\Controllers\PaymentProcessorController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\ConduceController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\PlantaGastoController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ReporteFiscalController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ImpresoraController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\DeliveryCompanyController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\ListaPrecioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\MesaController;
use App\Http\Controllers\MesaCategoriaController;
use App\Http\Controllers\MesaUbicacionController;
use App\Http\Controllers\ReservacionController;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\KdsController;
use App\Http\Controllers\RetencionExportController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\LavadorController;
use App\Http\Middleware\RoleMiddleware;

// Home / Welcome
Route::get('/', function () {
    return redirect()->route('login');
});

// Constraints: las rutas {modelo} solo matchean IDs numéricos.
// Esto evita que URLs como /compras/create matcheen /compras/{compra} antes.
Route::pattern('caja', '[0-9]+');
Route::pattern('cliente', '[0-9]+');
Route::pattern('proveedor', '[0-9]+');
Route::pattern('producto', '[0-9]+');
Route::pattern('compra', '[0-9]+');
Route::pattern('venta', '[0-9]+');
Route::pattern('mesa', '[0-9]+');
Route::pattern('almacen', '[0-9]+');
Route::pattern('detalle', '[0-9]+');
Route::pattern('movimiento', '[0-9]+');
Route::pattern('sesion', '[0-9]+');
Route::pattern('gasto', '[0-9]+');
Route::pattern('deliveryCompany', '[0-9]+');
Route::pattern('auditLog', '[0-9]+');
Route::pattern('backup', '[0-9]+');
Route::pattern('paymentProcessor', '[0-9]+');
Route::pattern('lavador', '[0-9]+');
Route::pattern('listaPrecio', '[0-9]+');
Route::pattern('sucursal', '[0-9]+');

// Dashboard
Route::middleware('auth')->group(function () {
    Route::post('/toggle-dark-mode', [HomeController::class, 'toggleDarkMode'])->name('toggleDarkMode');
    Route::post('/sucursal-activa', [HomeController::class, 'setSucursalActiva'])->name('sucursal.set-activa');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pdf', [HomeController::class, 'pdf'])->name('dashboard.pdf');
    Route::get('/dashboard/exportar', [HomeController::class, 'export'])->name('dashboard.exportar');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Operational routes
Route::middleware(['auth'])->group(function () {

    Route::get('/search', [SearchController::class, 'search'])->name('search.global');

    Route::get('/ventas/buscar-producto', [VentaController::class, 'buscarProducto'])->name('ventas.buscarProducto');
    Route::get('/ventas/buscar-codigo/{codigo}', [VentaController::class, 'buscarPorCodigoBarras'])->name('ventas.buscarPorCodigo');

    // Permission-based routes
    Route::middleware('permission:cajas.view')->group(function () {
        Route::get('/cajas', [CajaController::class, 'index'])->name('cajas.index');
        Route::get('/cajas/{caja}/cierre', [CajaController::class, 'resumenCierre'])->name('cajas.cierre');
        Route::get('/ventas/cuenta-abierta/{cliente_id}', [VentaController::class, 'getCuentaAbierta'])->name('ventas.getCuentaAbierta');
        Route::get('/ventas/json-dia', [VentaController::class, 'getStatsDia'])->name('ventas.statsDia');
        Route::get('/ventas/json-turno/{sesion}', [VentaController::class, 'getVentasTurno'])->name('ventas.jsonTurno');
    });

    Route::middleware('permission:cajas.create')->group(function () {
        Route::get('/cajas/crear', [CajaController::class, 'create'])->name('cajas.create');
        Route::post('/cajas', [CajaController::class, 'store'])->name('cajas.store');
    });

    Route::middleware('permission:cajas.edit')->group(function () {
        Route::get('/cajas/{caja}/editar', [CajaController::class, 'edit'])->name('cajas.edit');
        Route::put('/cajas/{caja}', [CajaController::class, 'update'])->name('cajas.update');
    });

    Route::middleware('permission:cajas.delete')->group(function () {
        Route::delete('/cajas/{caja}', [CajaController::class, 'destroy'])->name('cajas.destroy');
    });

    Route::middleware('permission:cajas.open')->group(function () {
        Route::post('/cajas/{caja}/abrir', [CajaController::class, 'abrir'])->name('cajas.abrir');
    });

    Route::middleware('permission:cajas.close')->group(function () {
        Route::post('/cajas/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
    });

    Route::post('/cajas/cambiar', [VentaController::class, 'cambiarCaja'])->name('cajas.cambiar');

    // Ventas
    Route::middleware('permission:ventas.create')->group(function () {
        Route::get('/ventas/create', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    });

    Route::middleware('permission:ventas.view,ventas.view.own')->group(function () {
        Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
    });

    Route::middleware('permission:ventas.anular')->group(function () {
        Route::delete('/ventas/{venta}', [VentaController::class, 'destroy'])->name('ventas.destroy');
    });

    Route::middleware('permission:ventas.export')->group(function () {
        Route::get('/ventas/exportar', [VentaController::class, 'exportExcel'])->name('ventas.exportar');
        Route::get('/ventas/pdf/all', [VentaController::class, 'exportAllPdf'])->name('ventas.pdf');
    });

    Route::middleware('permission:ventas.view,ventas.view.own')->group(function () {
        Route::get('/ventas/pdf/{id}', [VentaController::class, 'exportPdf'])->name('venta.pdf');
    });

    Route::post('/ventas/imprimir/{id}', [VentaController::class, 'imprimir'])->name('ventas.imprimir');
    Route::post('/ventas/facturar/{id}', [VentaController::class, 'facturar'])->name('ventas.facturar');

    // Cotizaciones
    Route::prefix('cotizaciones')->name('cotizaciones.')->group(function () {
        Route::get('/buscar-producto', [CotizacionController::class, 'buscarProductos'])->name('buscarProducto');
        Route::get('/{cotizacione}/pdf', [CotizacionController::class, 'pdf'])->name('pdf')->where('cotizacione', '[0-9]+');
        Route::get('/{cotizacione}/ticket', [CotizacionController::class, 'ticket'])->name('ticket')->where('cotizacione', '[0-9]+');
        Route::get('/{cotizacione}/ticket-text', [CotizacionController::class, 'ticketText'])->name('ticketText')->where('cotizacione', '[0-9]+');
        Route::post('/{cotizacione}/estado', [CotizacionController::class, 'cambiarEstado'])->name('cambiarEstado')->where('cotizacione', '[0-9]+');
        Route::post('/{cotizacione}/convertir', [CotizacionController::class, 'convertirAVenta'])->name('convertir')->where('cotizacione', '[0-9]+');
        Route::post('/{cotizacione}/enviar', [CotizacionController::class, 'enviar'])->name('enviar')->where('cotizacione', '[0-9]+');
    });

    Route::resource('cotizaciones', CotizacionController::class)->parameters([
        'cotizaciones' => 'cotizacione'
    ])->names('cotizaciones');

    // Conduces (Notas de Entrega)
    Route::prefix('conduces')->name('conduces.')->group(function () {
        Route::get('/from-venta/{venta}', [ConduceController::class, 'fromVenta'])->name('fromVenta')->where('venta', '[0-9]+');
        Route::post('/{conduce}/cambiar-estado', [ConduceController::class, 'cambiarEstado'])->name('cambiarEstado')->where('conduce', '[0-9]+');
        Route::post('/{conduce}/entregar', [ConduceController::class, 'entregar'])->name('entregar')->where('conduce', '[0-9]+');
        Route::get('/{conduce}/pdf', [ConduceController::class, 'pdf'])->name('pdf')->where('conduce', '[0-9]+');
        Route::get('/{conduce}/ticket', [ConduceController::class, 'ticket'])->name('ticket')->where('conduce', '[0-9]+');
        Route::get('/{conduce}/ticket-text', [ConduceController::class, 'ticketText'])->name('ticketText')->where('conduce', '[0-9]+');
    });
    Route::resource('conduces', ConduceController::class)->parameters([
        'conduces' => 'conduce'
    ])->names('conduces');

    // Productos
    Route::middleware('permission:productos.view')->group(function () {

        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/productos/import', [ProductoController::class, 'showImportForm'])->name('productos.import');
        Route::post('/productos/import/preview', [ProductoController::class, 'uploadPreview'])->name('productos.import.preview');
        Route::post('/productos/import/process', [ProductoController::class, 'processImport'])->name('productos.import.process');
        Route::get('/productos/exportar', [ProductoController::class, 'exportExcel'])->name('productos.exportar');
        Route::get('/productos/pdf', [ProductoController::class, 'exportPdf'])->name('productos.pdf');
        Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');

        Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::get('/categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
        Route::get('/categorias/{categoria}', [CategoriaController::class, 'show'])->name('categorias.show');
    });

    Route::middleware('permission:productos.create')->group(function () {
        Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::post('/productos/import', [ProductoController::class, 'uploadPreview'])->name('productos.import.store');

        Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    });

    Route::middleware('permission:productos.edit')->group(function () {
        Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');

        Route::get('/categorias/{categoria}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
        Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
    });

    Route::middleware('permission:productos.delete')->group(function () {
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

        Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
    });
    // Clientes
    Route::middleware('permission:clientes.view')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/exportar', [ClienteController::class, 'exportExcel'])->name('clientes.exportar');
        Route::get('/clientes/pdf', [ClienteController::class, 'pdf'])->name('clientes.pdf');
        Route::get('/cuentas-por-cobrar', [ClienteController::class, 'cuentas'])->name('clientes.cuentas');
        Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    });
    Route::middleware('permission:clientes.create')->group(function () {
        Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    });
    Route::middleware('permission:clientes.edit')->group(function () {
        Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
    });
    Route::middleware('permission:clientes.delete')->group(function () {
        Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    });

    // Pagos
    Route::middleware('permission:cobros.create')->group(function () {
        Route::get('/pagos/realizar/{venta}', [PagoController::class, 'realizar_pago'])->name('pagos.realizar');
        Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    });
    Route::middleware('permission:cobros.view')->group(function () {
        Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    });

    // Procesadores de Pago
    Route::middleware('permission:payment-processors.view')->group(function () {
        Route::get('/payment-processors', [PaymentProcessorController::class, 'index'])->name('payment-processors.index');
        Route::get('/payment-processors/{paymentProcessor}/edit', [PaymentProcessorController::class, 'edit'])->name('payment-processors.edit');
    });
    Route::middleware('permission:payment-processors.create')->group(function () {
        Route::get('/payment-processors/create', [PaymentProcessorController::class, 'create'])->name('payment-processors.create');
        Route::post('/payment-processors', [PaymentProcessorController::class, 'store'])->name('payment-processors.store');
    });
    Route::middleware('permission:payment-processors.edit')->group(function () {
        Route::put('/payment-processors/{paymentProcessor}', [PaymentProcessorController::class, 'update'])->name('payment-processors.update');
    });
    Route::middleware('permission:payment-processors.delete')->group(function () {
        Route::delete('/payment-processors/{paymentProcessor}', [PaymentProcessorController::class, 'destroy'])->name('payment-processors.destroy');
    });

    // Compras
    Route::middleware('permission:compras.view')->group(function () {
        Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
        Route::get('/compras/exportar', [CompraController::class, 'exportExcel'])->name('compras.exportar');
        Route::get('/compras/pdf', [CompraController::class, 'pdf'])->name('compras.pdf');
        Route::get('/compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    });
    Route::middleware('permission:compras.create')->group(function () {
        Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
        Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
    });
    Route::middleware('permission:compras.edit')->group(function () {
        Route::get('/compras/{compra}/edit', [CompraController::class, 'edit'])->name('compras.edit');
        Route::put('/compras/{compra}', [CompraController::class, 'update'])->name('compras.update');
        Route::delete('compras/{compra}/detalles/{detalle}', [CompraController::class, 'destroyDetalle'])->name('compras.detalle.destroy');
    });
    Route::middleware('permission:compras.delete')->group(function () {
        Route::delete('/compras/{compra}', [CompraController::class, 'destroy'])->name('compras.destroy');
    });

    Route::middleware('permission:compras.edit')->group(function () {
        Route::post('/compras/{compra}/generar-ecf', [CompraController::class, 'generarE41'])->name('compras.generar-ecf');
    });

    // Proveedores
    Route::middleware('permission:proveedores.view')->group(function () {
        Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('/proveedores/{proveedor}', [ProveedorController::class, 'show'])->name('proveedores.show');
    });
    Route::middleware('permission:proveedores.create')->group(function () {
        Route::get('/proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create');
        Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    });
    Route::middleware('permission:proveedores.edit')->group(function () {
        Route::get('/proveedores/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
        Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
    });
    Route::middleware('permission:proveedores.delete')->group(function () {
        Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    });
    Route::middleware('permission:proveedores.export')->group(function () {
        Route::get('/proveedores/exportar', [ProveedorController::class, 'exportExcel'])->name('proveedores.exportar');
        Route::get('/proveedores/pdf', [ProveedorController::class, 'pdf'])->name('proveedores.pdf');
    });

    // Almacenes
    Route::middleware('permission:almacenes.view')->group(function () {
        Route::get('/almacenes', [AlmacenController::class, 'index'])->name('almacenes.index');
        Route::get('/almacenes/{almacen}', [AlmacenController::class, 'show'])->name('almacenes.show');
    });
    Route::middleware('permission:almacenes.create')->group(function () {
        Route::get('/almacenes/create', [AlmacenController::class, 'create'])->name('almacenes.create');
        Route::post('/almacenes', [AlmacenController::class, 'store'])->name('almacenes.store');
    });
    Route::middleware('permission:almacenes.edit')->group(function () {
        Route::get('/almacenes/{almacen}/edit', [AlmacenController::class, 'edit'])->name('almacenes.edit');
        Route::put('/almacenes/{almacen}', [AlmacenController::class, 'update'])->name('almacenes.update');
    });
    Route::middleware('permission:almacenes.delete')->group(function () {
        Route::delete('/almacenes/{almacen}', [AlmacenController::class, 'destroy'])->name('almacenes.destroy');
    });
    Route::middleware('permission:almacenes.movements')->group(function () {
        Route::get('almacenes-movimientos', [AlmacenController::class, 'movimientos'])->name('almacenes.movimientos');
        Route::get('almacenes-movimientos/create', [AlmacenController::class, 'createMovimiento'])->name('almacenes.movimientos.create');
        Route::post('almacenes-movimientos', [AlmacenController::class, 'storeMovimiento'])->name('almacenes.movimientos.store');
    Route::get('almacenes-movimientos/pdf', [AlmacenController::class, 'exportMovimientosPdf'])->name('almacenes.movimientos.pdf');
    Route::get('almacenes-movimientos/excel', [AlmacenController::class, 'exportMovimientosExcel'])->name('almacenes.movimientos.excel');
    Route::get('/almacenes/inventario-almacen', [AlmacenController::class, 'inventarioAlmacen'])->name('almacenes.inventario');
    });

    // Kardex
    Route::middleware('permission:kardex.view')->group(function () {
        Route::get('/kardex', [KardexController::class, 'index'])->name('kardex.index');
    });

    // NCF
    Route::middleware('permission:ncf.view')->group(function () {
        Route::get('/ncf', [NcfController::class, 'index'])->name('ncf.index');
        Route::get('/ncf/{ncf}', [NcfController::class, 'show'])->name('ncf.show');
    });
    Route::middleware('permission:ncf.manage')->group(function () {
        Route::get('/ncf/create', [NcfController::class, 'create'])->name('ncf.create');
        Route::post('/ncf', [NcfController::class, 'store'])->name('ncf.store');
        Route::get('/ncf/{ncf}/edit', [NcfController::class, 'edit'])->name('ncf.edit');
        Route::put('/ncf/{ncf}', [NcfController::class, 'update'])->name('ncf.update');
        Route::delete('/ncf/{ncf}', [NcfController::class, 'destroy'])->name('ncf.destroy');
        Route::post('ncf/{ncf}/toggle', [NcfController::class, 'toggleStatus'])->name('ncf.toggle');
    });

    // Gastos
    Route::middleware('permission:gastos.view')->group(function () {
        Route::get('/gastos', [GastoController::class, 'index'])->name('gastos.index');
        Route::get('/gastos/{gasto}', [GastoController::class, 'show'])->name('gastos.show');
    });
    Route::middleware('permission:gastos.create')->group(function () {
        Route::get('/gastos/create', [GastoController::class, 'create'])->name('gastos.create');
        Route::post('/gastos', [GastoController::class, 'store'])->name('gastos.store');
    });
    Route::middleware('permission:gastos.edit')->group(function () {
        Route::get('/gastos/{gasto}/edit', [GastoController::class, 'edit'])->name('gastos.edit');
        Route::put('/gastos/{gasto}', [GastoController::class, 'update'])->name('gastos.update');
    });
    Route::middleware('permission:gastos.delete')->group(function () {
        Route::delete('/gastos/{gasto}', [GastoController::class, 'destroy'])->name('gastos.destroy');
    });

    // Auditoría
    Route::middleware('permission:auditoria.view')->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });

    // Plantilla de Gastos
    Route::middleware('permission:plantilla-gastos.view')->group(function () {
        Route::get('/plantilla-gastos', [PlantaGastoController::class, 'index'])->name('plantilla-gastos.index');
        Route::get('/plantilla-gastos/{plantilla-gasto}', [PlantaGastoController::class, 'show'])->name('plantilla-gastos.show');
        Route::post('/plantilla-gastos/{plantilla-gasto}/activar', [PlantaGastoController::class, 'activar'])->name('plantilla-gastos.activar');
        Route::post('/plantilla-gastos/{plantilla-gasto}/desactivar', [PlantaGastoController::class, 'desactivar'])->name('plantilla-gastos.desactivar');
    });
    Route::middleware('permission:plantilla-gastos.create')->group(function () {
        Route::get('/plantilla-gastos/create', [PlantaGastoController::class, 'create'])->name('plantilla-gastos.create');
        Route::post('/plantilla-gastos', [PlantaGastoController::class, 'store'])->name('plantilla-gastos.store');
    });
    Route::middleware('permission:plantilla-gastos.edit')->group(function () {
        Route::get('/plantilla-gastos/{plantilla-gasto}/edit', [PlantaGastoController::class, 'edit'])->name('plantilla-gastos.edit');
        Route::put('/plantilla-gastos/{plantilla-gasto}', [PlantaGastoController::class, 'update'])->name('plantilla-gastos.update');
    });
    Route::middleware('permission:plantilla-gastos.delete')->group(function () {
        Route::delete('/plantilla-gastos/{plantilla-gasto}', [PlantaGastoController::class, 'destroy'])->name('plantilla-gastos.destroy');
    });

    // Backups
    Route::middleware('permission:backups.view')->group(function () {
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::get('/backups/config', [BackupController::class, 'config'])->name('backups.config');
        Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    });
    Route::middleware('permission:backups.create')->group(function () {
        Route::post('/backups', [BackupController::class, 'create'])->name('backups.store');
    });
    Route::middleware('permission:backups.delete')->group(function () {
        Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });

    // Reportes
    Route::middleware('permission:reportes.view')->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

        // Ventas
        Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
        Route::get('/reportes/ventas/csv', [ReporteController::class, 'ventasCsv'])->name('reportes.ventas.csv');
        Route::get('/reportes/ventas/pdf', [ReporteController::class, 'ventasPdf'])->name('reportes.ventas.pdf');

        // Compras
        Route::get('/reportes/compras', [ReporteController::class, 'compras'])->name('reportes.compras');
        Route::get('/reportes/compras/csv', [ReporteController::class, 'comprasCsv'])->name('reportes.compras.csv');
        Route::get('/reportes/compras/pdf', [ReporteController::class, 'comprasPdf'])->name('reportes.compras.pdf');

        // Stock
        Route::get('/reportes/stock', [ReporteController::class, 'stock'])->name('reportes.stock');
        Route::get('/reportes/stock/csv', [ReporteController::class, 'stockCsv'])->name('reportes.stock.csv');
        Route::get('/reportes/stock/pdf', [ReporteController::class, 'stockPdf'])->name('reportes.stock.pdf');

        // Caja
        Route::get('/reportes/caja', [ReporteController::class, 'caja'])->name('reportes.caja');
        Route::get('/reportes/caja/csv', [ReporteController::class, 'cajaCsv'])->name('reportes.caja.csv');

        // Utilidades
        Route::get('/reportes/utilidades', [ReporteController::class, 'utilidades'])->name('reportes.utilidades');
        Route::get('/reportes/utilidades/csv', [ReporteController::class, 'utilidadesCsv'])->name('reportes.utilidades.csv');

        // Retenciones
        Route::get('/reportes/retenciones', [ReporteController::class, 'retenciones'])->name('reportes.retenciones');
        Route::get('/reportes/retenciones/csv', [ReporteController::class, 'retencionesCsv'])->name('reportes.retenciones.csv');

        // Fiscales (606/607) - existing
        Route::get('/reportes/fiscales', [ReporteFiscalController::class, 'index'])->name('reportes.fiscales');
        Route::get('/reportes/fiscales/export', [ReporteFiscalController::class, 'exportCsv'])->name('reportes.fiscales.export');
        Route::get('/reportes/fiscales/txt', [ReporteFiscalController::class, 'exportTxt'])->name('reportes.fiscales.txt');
        Route::get('/reportes/fiscales/pdf', [ReporteFiscalController::class, 'exportPdf'])->name('reportes.fiscales.pdf');
        Route::get('/reportes/resumen', [ReporteFiscalController::class, 'resumen'])->name('reportes.resumen');
        // Restaurante
        Route::get('/reportes/restaurante', [ReporteController::class, 'restaurante'])->name('reportes.restaurante');
        Route::get('/reportes/propinas', [ReporteController::class, 'propinas'])->name('reportes.propinas');
        Route::get('/reportes/delivery-comisiones', [ReporteController::class, 'comisionesDelivery'])->name('reportes.delivery-comisiones');
    });

    // Configuración
    Route::middleware('permission:configuracion.view')->group(function () {
        Route::get('/configuracion', [ConfigurationController::class, 'index'])->name('configuracion.index');
    });
    Route::middleware('permission:configuracion.edit')->group(function () {
        Route::post('/configuracion', [ConfigurationController::class, 'update'])->name('configuracion.update');
        Route::post('/configuracion/test-email', [ConfigurationController::class, 'testEmail'])->name('configuracion.test-email');
    });

    // Tipos de Negocio
    Route::middleware('permission:configuracion.view')->group(function () {
        Route::get('/business-types', [BusinessTypeController::class, 'index'])->name('business-types.index');
        Route::get('/business-types/{businessType}/modules-data', [BusinessTypeController::class, 'modulesData'])->name('business-types.modules-data');
    });
    Route::middleware('permission:configuracion.edit')->group(function () {
        Route::post('/business-types', [BusinessTypeController::class, 'store'])->name('business-types.store');
        Route::put('/business-types/{businessType}', [BusinessTypeController::class, 'update'])->name('business-types.update');
        Route::delete('/business-types/{businessType}', [BusinessTypeController::class, 'destroy'])->name('business-types.destroy');
        Route::post('/business-types/{businessType}/modules', [BusinessTypeController::class, 'updateModules'])->name('business-types.modules');
    });

    // Módulos
    Route::middleware('permission:configuracion.view')->group(function () {
        Route::get('/modulos', [ModuloController::class, 'index'])->name('modulos.index');
    });
    Route::middleware('permission:configuracion.edit')->group(function () {
        Route::post('/modulos', [ModuloController::class, 'store'])->name('modulos.store');
        Route::put('/modulos/{modulo}', [ModuloController::class, 'update'])->name('modulos.update');
        Route::delete('/modulos/{modulo}', [ModuloController::class, 'destroy'])->name('modulos.destroy');
        Route::post('/modulos/{modulo}/toggle', [ModuloController::class, 'toggle'])->name('modulos.toggle');
    });

    // e-CF (Comprobante Fiscal Electrónico - DGII)
    Route::middleware('permission:ecf.view')->group(function () {
        Route::get('/ecf', [EcfController::class, 'index'])->name('ecf.index');
        Route::get('/ecf/{ecf}', [EcfController::class, 'show'])->name('ecf.show');
        Route::get('/ecf/{ecf}/xml', [EcfController::class, 'xml'])->name('ecf.xml');
        Route::get('/ecf/{ecf}/pdf', [EcfController::class, 'pdf'])->name('ecf.pdf');
    });
    Route::middleware('permission:ecf.send')->group(function () {
        Route::post('/ecf/{ecf}/firmar', [EcfController::class, 'firmar'])->name('ecf.firmar');
        Route::post('/ecf/{ecf}/enviar', [EcfController::class, 'enviar'])->name('ecf.enviar');
        Route::post('/ecf/{ecf}/consultar', [EcfController::class, 'consultar'])->name('ecf.consultar');
        Route::get('/ecf/validar-rnc', [EcfController::class, 'validarRnc'])->name('ecf.validar-rnc');
    });
    Route::middleware('permission:ecf.manage')->group(function () {
        Route::post('/ecf/{ecf}/anular', [EcfController::class, 'anular'])->name('ecf.anular');
        Route::post('/ecf/{ecf}/nota-debito', [EcfController::class, 'notaDebito'])->name('ecf.nota-debito');
        Route::get('/secuencias-ecf', [SecuenciaEcfController::class, 'index'])->name('secuencias-ecf.index');
        Route::get('/secuencias-ecf/create', [SecuenciaEcfController::class, 'create'])->name('secuencias-ecf.create');
        Route::post('/secuencias-ecf', [SecuenciaEcfController::class, 'store'])->name('secuencias-ecf.store');
        Route::get('/secuencias-ecf/{secuencia_ecf}/edit', [SecuenciaEcfController::class, 'edit'])->name('secuencias-ecf.edit');
        Route::put('/secuencias-ecf/{secuencia_ecf}', [SecuenciaEcfController::class, 'update'])->name('secuencias-ecf.update');
        Route::delete('/secuencias-ecf/{secuencia_ecf}', [SecuenciaEcfController::class, 'destroy'])->name('secuencias-ecf.destroy');
        Route::post('/secuencias-ecf/{secuencia_ecf}/toggle', [SecuenciaEcfController::class, 'toggle'])->name('secuencias-ecf.toggle');
    });
    Route::middleware('permission:ecf.certificados')->group(function () {
        Route::get('/certificados-digitales', [CertificadoDigitalController::class, 'index'])->name('certificados-digitales.index');
        Route::get('/certificados-digitales/create', [CertificadoDigitalController::class, 'create'])->name('certificados-digitales.create');
        Route::post('/certificados-digitales', [CertificadoDigitalController::class, 'store'])->name('certificados-digitales.store');
        Route::get('/certificados-digitales/{certificado}', [CertificadoDigitalController::class, 'show'])->name('certificados-digitales.show');
        Route::get('/certificados-digitales/{certificado}/edit', [CertificadoDigitalController::class, 'edit'])->name('certificados-digitales.edit');
        Route::put('/certificados-digitales/{certificado}', [CertificadoDigitalController::class, 'update'])->name('certificados-digitales.update');
        Route::delete('/certificados-digitales/{certificado}', [CertificadoDigitalController::class, 'destroy'])->name('certificados-digitales.destroy');
        Route::post('/certificados-digitales/{certificado}/toggle', [CertificadoDigitalController::class, 'toggle'])->name('certificados-digitales.toggle');
    });

    // Impresoras
    Route::middleware('permission:impresoras.view')->group(function () {
        Route::get('/impresoras', [ImpresoraController::class, 'index'])->name('impresoras.index');
        Route::get('/impresoras/create', [ImpresoraController::class, 'create'])->name('impresoras.create');
        Route::post('/impresoras', [ImpresoraController::class, 'store'])->name('impresoras.store');
        Route::get('/impresoras/{impresora}/edit', [ImpresoraController::class, 'edit'])->name('impresoras.edit');
        Route::put('/impresoras/{impresora}', [ImpresoraController::class, 'update'])->name('impresoras.update');
        Route::delete('/impresoras/{impresora}', [ImpresoraController::class, 'destroy'])->name('impresoras.destroy');
        Route::post('/impresoras/{impresora}/probar', [ImpresoraController::class, 'probar'])->name('impresoras.probar');
        Route::post('/impresoras/{impresora}/toggle-auto/{modulo}', [ImpresoraController::class, 'toggleAuto'])->name('impresoras.toggle-auto');
        Route::get('/impresoras/historial', [ImpresoraController::class, 'historial'])->name('impresoras.historial');
        Route::get('/impresoras/plantillas', [ImpresoraController::class, 'plantillas'])->name('impresoras.plantillas');
        Route::post('/impresoras/plantillas/{plantilla}', [ImpresoraController::class, 'plantillaUpdate'])->name('impresoras.plantilla-update');
        Route::get('/impresoras/print-dialog', [ImpresoraController::class, 'printDialog'])->name('impresoras.print-dialog');
        Route::post('/impresoras/print-direct', [ImpresoraController::class, 'printDirect'])->name('impresoras.print-direct');
    });
});

// Admin only routes (using Spatie role admin)
Route::middleware(['auth', 'role:admin|owner'])->group(function () {
    Route::resource('usuarios', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::get('roles-matrix', [RoleController::class, 'matrix'])->name('roles.matrix');
});

// Admin de Instancia (admin-business) - gestión de usuarios solo en su instancia
// Instance user management routes removed for admin-business role
// Route::get('/users', [\App\Http\Controllers\OwnerController::class, 'instanceUsersIndex'])->name('users.index');
// Route::get('/users/create', [\App\Http\Controllers\OwnerController::class, 'instanceUserCreate'])->name('users.create');
// Route::post('/users', [\App\Http\Controllers\OwnerController::class, 'instanceUserStore'])->name('users.store');
// Route::get('/users/{user}/edit', [\App\Http\Controllers\OwnerController::class, 'instanceUserEdit'])->name('users.edit');
// Route::put('/users/{user}', [\App\Http\Controllers\OwnerController::class, 'instanceUserUpdate'])->name('users.update');
// Route::delete('/users/{user}', [\App\Http\Controllers\OwnerController::class, 'instanceUserDestroy'])->name('users.destroy');


// Owner (Dueño del Sistema)
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/', [\App\Http\Controllers\OwnerController::class, 'index'])->name('dashboard');
    Route::get('/business-types', [\App\Http\Controllers\OwnerController::class, 'businessTypes'])->name('business-types.index');
    Route::get('/business-types/create', [\App\Http\Controllers\OwnerController::class, 'businessTypesCreate'])->name('business-types.create');
    Route::post('/business-types', [\App\Http\Controllers\OwnerController::class, 'businessTypesStore'])->name('business-types.store');
    Route::get('/business-types/{type}/edit', [\App\Http\Controllers\OwnerController::class, 'businessTypesEdit'])->name('business-types.edit');
    Route::put('/business-types/{type}', [\App\Http\Controllers\OwnerController::class, 'businessTypesUpdate'])->name('business-types.update');
    Route::delete('/business-types/{type}', [\App\Http\Controllers\OwnerController::class, 'businessTypesDestroy'])->name('business-types.destroy');
    // Module management
    Route::get('/modules', [\App\Http\Controllers\OwnerController::class, 'modulesIndex'])->name('modules.index');
    Route::get('/modules/create', [\App\Http\Controllers\OwnerController::class, 'modulesCreate'])->name('modules.create');
    Route::post('/modules', [\App\Http\Controllers\OwnerController::class, 'modulesStore'])->name('modules.store');
    Route::get('/modules/{module}/edit', [\App\Http\Controllers\OwnerController::class, 'modulesEdit'])->name('modules.edit');
    Route::put('/modules/{module}', [\App\Http\Controllers\OwnerController::class, 'modulesUpdate'])->name('modules.update');
    Route::delete('/modules/{module}', [\App\Http\Controllers\OwnerController::class, 'modulesDestroy'])->name('modules.destroy');
    Route::get('/instances', [\App\Http\Controllers\OwnerController::class, 'instances'])->name('instances.index');
    Route::get('/instances/create', [\App\Http\Controllers\OwnerController::class, 'instancesCreate'])->name('instances.create');
    Route::post('/instances', [\App\Http\Controllers\OwnerController::class, 'instancesStore'])->name('instances.store');
    Route::get('/instances/{instance}', [\App\Http\Controllers\OwnerController::class, 'instancesShow'])->name('instances.show');
    Route::get('/instances/{instance}/edit', [\App\Http\Controllers\OwnerController::class, 'instancesEdit'])->name('instances.edit');
    Route::put('/instances/{instance}', [\App\Http\Controllers\OwnerController::class, 'instancesUpdate'])->name('instances.update');
    Route::delete('/instances/{instance}', [\App\Http\Controllers\OwnerController::class, 'instancesDestroy'])->name('instances.destroy');
    Route::get('/instances/{instance}/config', [\App\Http\Controllers\OwnerController::class, 'instancesConfig'])->name('instances.config');
    Route::put('/instances/{instance}/config', [\App\Http\Controllers\OwnerController::class, 'instancesConfigUpdate'])->name('instances.config.update');
    Route::post('/instances/{instance}/toggle-block', [\App\Http\Controllers\OwnerController::class, 'alternarBloqueo'])->name('instances.toggle-block');
    Route::post('/instances/{instance}/clean', [\App\Http\Controllers\OwnerController::class, 'cleanInstance'])->name('instances.clean');
    Route::get('/instances/{instance}/pagos', [\App\Http\Controllers\OwnerController::class, 'paymentHistory'])->name('instances.pagos');
    Route::get('/instances/{instance}/pagos/create', [\App\Http\Controllers\OwnerController::class, 'registerPayment'])->name('instances.pagos.create');
    Route::post('/instances/{instance}/pagos', [\App\Http\Controllers\OwnerController::class, 'storePayment'])->name('instances.pagos.store');
    // Instance user management
    Route::get('/instances/{instance}/users/create', [\App\Http\Controllers\OwnerController::class, 'instanceUserCreate'])->name('instances.users.create');
    Route::post('/instances/{instance}/users', [\App\Http\Controllers\OwnerController::class, 'instanceUserStore'])->name('instances.users.store');
    Route::get('/instances/{instance}/users/{user}/edit', [\App\Http\Controllers\OwnerController::class, 'instanceUserEdit'])->name('instances.users.edit');
    Route::put('/instances/{instance}/users/{user}', [\App\Http\Controllers\OwnerController::class, 'instanceUserUpdate'])->name('instances.users.update');
    Route::delete('/instances/{instance}/users/{user}', [\App\Http\Controllers\OwnerController::class, 'instanceUserDestroy'])->name('instances.users.destroy');
    // Instance role management
    Route::get('/instances/{instance}/roles', [\App\Http\Controllers\OwnerController::class, 'instanceRoles'])->name('instances.roles');
    Route::get('/instances/{instance}/roles/create', [\App\Http\Controllers\OwnerController::class, 'instanceRolesCreate'])->name('instances.roles.create');
    Route::post('/instances/{instance}/roles', [\App\Http\Controllers\OwnerController::class, 'instanceRolesStore'])->name('instances.roles.store');
    Route::get('/instances/{instance}/roles/{role}/edit', [\App\Http\Controllers\OwnerController::class, 'instanceRolesEdit'])->name('instances.roles.edit');
    Route::put('/instances/{instance}/roles/{role}', [\App\Http\Controllers\OwnerController::class, 'instanceRolesUpdate'])->name('instances.roles.update');
    Route::delete('/instances/{instance}/roles/{role}', [\App\Http\Controllers\OwnerController::class, 'instanceRolesDestroy'])->name('instances.roles.destroy');
    // Instance error logs
    Route::get('/errors', [\App\Http\Controllers\OwnerController::class, 'globalErrors'])->name('errors.index');
    Route::get('/instances/{instance}/errors', [\App\Http\Controllers\OwnerController::class, 'instanceErrors'])->name('instances.errors');
    Route::patch('/instances/{instance}/errors/{errorLog}/resolve', [\App\Http\Controllers\OwnerController::class, 'resolveError'])->name('instances.errors.resolve');
    Route::delete('/instances/{instance}/errors', [\App\Http\Controllers\OwnerController::class, 'clearErrors'])->name('instances.errors.clear');
    // Online users
    Route::get('/online', [\App\Http\Controllers\OwnerController::class, 'onlineUsers'])->name('online.index');
    Route::get('/instances/{instance}/online', [\App\Http\Controllers\OwnerController::class, 'instanceOnlineUsers'])->name('instances.online');

    // (owner role management removed — roles are managed per-instance)
});

// Devoluciones
Route::middleware(['auth', 'permission:devoluciones.view'])->group(function () {
    Route::get('devoluciones', [DevolucionController::class, 'index'])->name('devoluciones.index');
    Route::get('devoluciones/create', [DevolucionController::class, 'create'])->name('devoluciones.create');
    Route::post('devoluciones', [DevolucionController::class, 'store'])->name('devoluciones.store');
    Route::get('devoluciones/{devolucion}', [DevolucionController::class, 'show'])->name('devoluciones.show');
    Route::post('devoluciones/{devolucion}/confirmar', [DevolucionController::class, 'confirmar'])->name('devoluciones.confirmar');
    Route::post('devoluciones/{devolucion}/generar-nc', [DevolucionController::class, 'generarNotaCredito'])->name('devoluciones.generar-nc');
    Route::delete('devoluciones/{devolucion}', [DevolucionController::class, 'destroy'])->name('devoluciones.destroy');
    Route::get('devoluciones/buscar-venta', [DevolucionController::class, 'buscarVenta'])->name('devoluciones.buscar-venta');
});

// Listas de Precios
Route::middleware(['auth', 'permission:listas-precio.view'])->group(function () {
    Route::get('listas-precio', [ListaPrecioController::class, 'index'])->name('listas-precio.index');
    Route::get('listas-precio/{listaPrecio}', [ListaPrecioController::class, 'show'])->name('listas-precio.show');
});
Route::middleware(['auth', 'permission:listas-precio.create'])->group(function () {
    Route::get('listas-precio/create', [ListaPrecioController::class, 'create'])->name('listas-precio.create');
    Route::post('listas-precio', [ListaPrecioController::class, 'store'])->name('listas-precio.store');
});
Route::middleware(['auth', 'permission:listas-precio.edit'])->group(function () {
    Route::get('listas-precio/{listaPrecio}/edit', [ListaPrecioController::class, 'edit'])->name('listas-precio.edit');
    Route::put('listas-precio/{listaPrecio}', [ListaPrecioController::class, 'update'])->name('listas-precio.update');
    Route::post('listas-precio/{listaPrecio}/actualizar-precios', [ListaPrecioController::class, 'actualizarPrecios'])->name('listas-precio.actualizar-precios');
    Route::post('listas-precio/{listaPrecio}/duplicar', [ListaPrecioController::class, 'duplicar'])->name('listas-precio.duplicar');
    Route::post('listas-precio/{listaPrecio}/quitar-producto/{item}', [ListaPrecioController::class, 'quitarProducto'])->name('listas-precio.quitar-producto');
});
Route::middleware(['auth', 'permission:listas-precio.delete'])->group(function () {
    Route::delete('listas-precio/{listaPrecio}', [ListaPrecioController::class, 'destroy'])->name('listas-precio.destroy');
});

// Sucursales
Route::middleware(['auth', 'permission:sucursales.view'])->group(function () {
    Route::get('sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
    Route::get('sucursales/{sucursal}', [SucursalController::class, 'show'])->name('sucursales.show');
});
Route::middleware(['auth', 'permission:sucursales.create'])->group(function () {
    Route::get('sucursales/create', [SucursalController::class, 'create'])->name('sucursales.create');
    Route::post('sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
});
Route::middleware(['auth', 'permission:sucursales.edit'])->group(function () {
    Route::get('sucursales/{sucursal}/edit', [SucursalController::class, 'edit'])->name('sucursales.edit');
    Route::put('sucursales/{sucursal}', [SucursalController::class, 'update'])->name('sucursales.update');
});
Route::middleware(['auth', 'permission:sucursales.delete'])->group(function () {
    Route::delete('sucursales/{sucursal}', [SucursalController::class, 'destroy'])->name('sucursales.destroy');
});

// Delivery Companies
Route::middleware(['auth', 'permission:delivery-companies.view'])->group(function () {
    Route::get('delivery-companies', [DeliveryCompanyController::class, 'index'])->name('delivery-companies.index');
    Route::get('delivery-companies/listar-activas', [DeliveryCompanyController::class, 'listarActivas'])->name('delivery-companies.listar-activas');
});
Route::middleware(['auth', 'permission:delivery-companies.create'])->group(function () {
    Route::get('delivery-companies/create', [DeliveryCompanyController::class, 'create'])->name('delivery-companies.create');
    Route::post('delivery-companies', [DeliveryCompanyController::class, 'store'])->name('delivery-companies.store');
});
Route::middleware(['auth', 'permission:delivery-companies.edit'])->group(function () {
    Route::get('delivery-companies/{deliveryCompany}/edit', [DeliveryCompanyController::class, 'edit'])->name('delivery-companies.edit');
    Route::put('delivery-companies/{deliveryCompany}', [DeliveryCompanyController::class, 'update'])->name('delivery-companies.update');
});
Route::middleware(['auth', 'permission:delivery-companies.delete'])->group(function () {
    Route::delete('delivery-companies/{deliveryCompany}', [DeliveryCompanyController::class, 'destroy'])->name('delivery-companies.destroy');
});

// Restaurante (Terminal de Mesas)
Route::middleware(['auth'])->group(function () {
    Route::get('/restaurante', [RestauranteController::class, 'index'])->name('restaurante.index')->middleware('permission:restaurante.view');

    // POS — Ordenes
    Route::get('/restaurante/mesa/{mesa}', [OrdenController::class, 'getMesa'])->name('restaurante.mesa')->middleware('permission:restaurante.view');
    Route::get('/restaurante/productos', [OrdenController::class, 'buscarProducto'])->name('restaurante.productos')->middleware('permission:restaurante.view');
    Route::get('/restaurante/catalogo', [OrdenController::class, 'catalogo'])->name('restaurante.catalogo')->middleware('permission:restaurante.view');
    Route::get('/restaurante/sesion-activa', [OrdenController::class, 'sesionActiva'])->name('restaurante.sesion-activa')->middleware('permission:restaurante.view');
    Route::get('/restaurante/productos/populares', [OrdenController::class, 'populares'])->name('restaurante.productos.populares')->middleware('permission:restaurante.view');
    Route::get('/restaurante/mesa/{mesa}/ticket', [OrdenController::class, 'ticket'])->name('restaurante.mesa.ticket')->middleware('permission:restaurante.view');
    Route::post('/restaurante/mesa/{mesa}/ticket/print', [OrdenController::class, 'imprimirTicket'])->name('restaurante.mesa.ticket.print')->middleware('permission:restaurante.cobrar');
    Route::get('/restaurante/mesa/{mesa}/ticket-text', [OrdenController::class, 'ticketText'])->name('restaurante.mesa.ticket-text')->middleware('permission:restaurante.view');
    Route::get('/restaurante/mesa/{mesa}/historial', [OrdenController::class, 'historialMesa'])->name('restaurante.mesa.historial')->middleware('permission:restaurante.view');
    Route::post('/restaurante/mesa/{mesa}/abrir', [OrdenController::class, 'abrirMesa'])->name('restaurante.mesa.abrir')->middleware('permission:restaurante.cobrar');
    Route::post('/restaurante/mesa/{mesa}/agregar', [OrdenController::class, 'agregarItem'])->name('restaurante.mesa.agregar')->middleware('permission:restaurante.cobrar');
    Route::post('/restaurante/mesa/{mesa}/actualizar/{detalle}', [OrdenController::class, 'actualizarItem'])->name('restaurante.mesa.actualizar')->middleware('permission:restaurante.cobrar');
    Route::delete('/restaurante/mesa/{mesa}/quitar/{detalle}', [OrdenController::class, 'quitarItem'])->name('restaurante.mesa.quitar')->middleware('permission:restaurante.cobrar');
    Route::post('/restaurante/mesa/{mesa}/cobrar', [OrdenController::class, 'cobrar'])->name('restaurante.mesa.cobrar')->middleware('permission:restaurante.cobrar');
    Route::post('/restaurante/mesa/{mesa}/facturar', [OrdenController::class, 'facturar'])->name('restaurante.mesa.facturar')->middleware('permission:restaurante.cobrar');
    Route::post('/restaurante/mesa/{mesa}/trasladar', [OrdenController::class, 'trasladarMesa'])->name('restaurante.mesa.trasladar')->middleware('permission:restaurante.cobrar');
    Route::post('/restaurante/mesa/{mesa}/anular', [OrdenController::class, 'anularOrden'])->name('restaurante.mesa.anular')->middleware('permission:restaurante.anular');
    Route::post('/restaurante/mesa/{mesa}/descuento', [OrdenController::class, 'aplicarDescuento'])->name('restaurante.mesa.descuento')->middleware('permission:restaurante.descuento');
    Route::post('/restaurante/mesa/{mesa}/estado', [OrdenController::class, 'cambiarEstado'])->name('restaurante.mesa.estado')->middleware('permission:restaurante.mesas.manage');
    Route::post('/restaurante/mesa/{mesa}/posicion', [OrdenController::class, 'savePosicion'])->name('restaurante.mesa.posicion')->middleware('permission:restaurante.mesas.manage');
    Route::post('/restaurante/mesas/posiciones', [OrdenController::class, 'saveAllPosiciones'])->name('restaurante.mesas.posiciones')->middleware('permission:restaurante.mesas.manage');

    // Cajas desde restaurante
    Route::post('/restaurante/abrir-caja', [OrdenController::class, 'abrirCaja'])->name('restaurante.abrir-caja')->middleware('permission:restaurante.cajas');
    Route::post('/restaurante/crear-caja', [OrdenController::class, 'crearCaja'])->name('restaurante.crear-caja')->middleware('permission:restaurante.cajas');
    Route::get('/restaurante/cajas', [OrdenController::class, 'cajasDisponibles'])->name('restaurante.cajas')->middleware('permission:restaurante.cajas');
    Route::get('/restaurante/caja/resumen', [OrdenController::class, 'resumenCierre'])->name('restaurante.caja.resumen')->middleware('permission:restaurante.cajas');
    Route::post('/restaurante/caja/cerrar', [OrdenController::class, 'cerrarCaja'])->name('restaurante.caja.cerrar')->middleware('permission:restaurante.cajas');

    // Gestión de Mesas
    Route::get('/restaurante/mesas', [MesaController::class, 'index'])->name('restaurante.mesas.index')->middleware('permission:restaurante.mesas.manage');
    Route::get('/restaurante/mesas/{mesa}', [MesaController::class, 'show'])->name('restaurante.mesas.show')->middleware('permission:restaurante.mesas.manage');
    Route::post('/restaurante/mesa', [MesaController::class, 'store'])->name('restaurante.mesa.store')->middleware('permission:restaurante.mesas.manage');
    Route::put('/restaurante/mesa/{mesa}/update', [MesaController::class, 'update'])->name('restaurante.mesa.update')->middleware('permission:restaurante.mesas.manage');
    Route::delete('/restaurante/mesa/{mesa}', [MesaController::class, 'destroy'])->name('restaurante.mesa.destroy')->middleware('permission:restaurante.mesas.manage');

    // Categorías de mesa
    Route::get('/restaurante/categorias', [MesaCategoriaController::class, 'index'])->name('restaurante.categorias.index')->middleware('permission:restaurante.categorias');
    Route::get('/restaurante/categorias/{categoria}', [MesaCategoriaController::class, 'show'])->name('restaurante.categorias.show')->middleware('permission:restaurante.categorias');
    Route::post('/restaurante/categorias', [MesaCategoriaController::class, 'store'])->name('restaurante.categorias.store')->middleware('permission:restaurante.categorias');
    Route::put('/restaurante/categorias/{categoria}', [MesaCategoriaController::class, 'update'])->name('restaurante.categorias.update')->middleware('permission:restaurante.categorias');
    Route::delete('/restaurante/categorias/{categoria}', [MesaCategoriaController::class, 'destroy'])->name('restaurante.categorias.destroy')->middleware('permission:restaurante.categorias');

    // Ubicaciones de mesas
    Route::get('/restaurante/ubicaciones', [MesaUbicacionController::class, 'index'])->name('restaurante.ubicaciones.index')->middleware('permission:restaurante.categorias');
    Route::get('/restaurante/ubicaciones/{mesaUbicacion}', [MesaUbicacionController::class, 'show'])->name('restaurante.ubicaciones.show')->middleware('permission:restaurante.categorias');
    Route::post('/restaurante/ubicaciones', [MesaUbicacionController::class, 'store'])->name('restaurante.ubicaciones.store')->middleware('permission:restaurante.categorias');
    Route::put('/restaurante/ubicaciones/{mesaUbicacion}', [MesaUbicacionController::class, 'update'])->name('restaurante.ubicaciones.update')->middleware('permission:restaurante.categorias');
    Route::delete('/restaurante/ubicaciones/{mesaUbicacion}', [MesaUbicacionController::class, 'destroy'])->name('restaurante.ubicaciones.destroy')->middleware('permission:restaurante.categorias');

    // Reservaciones
    Route::get('/restaurante/reservaciones', [ReservacionController::class, 'index'])->name('restaurante.reservaciones.index')->middleware('permission:restaurante.reservaciones');
    Route::post('/restaurante/reservaciones', [ReservacionController::class, 'store'])->name('restaurante.reservaciones.store')->middleware('permission:restaurante.reservaciones');
    Route::put('/restaurante/reservaciones/{reservacion}', [ReservacionController::class, 'update'])->name('restaurante.reservaciones.update')->middleware('permission:restaurante.reservaciones');
    Route::patch('/restaurante/reservaciones/{reservacion}/estado', [ReservacionController::class, 'estado'])->name('restaurante.reservaciones.estado')->middleware('permission:restaurante.reservaciones');
    Route::delete('/restaurante/reservaciones/{reservacion}', [ReservacionController::class, 'destroy'])->name('restaurante.reservaciones.destroy')->middleware('permission:restaurante.reservaciones');

    // Waitlist
    Route::get('/restaurante/waitlist', [WaitlistController::class, 'index'])->name('restaurante.waitlist.index')->middleware('permission:restaurante.view');
    Route::post('/restaurante/waitlist', [WaitlistController::class, 'store'])->name('restaurante.waitlist.store')->middleware('permission:restaurante.cobrar');
    Route::patch('/restaurante/waitlist/{entry}/estado', [WaitlistController::class, 'updateEstado'])->name('restaurante.waitlist.estado')->middleware('permission:restaurante.cobrar');
    Route::delete('/restaurante/waitlist/{entry}', [WaitlistController::class, 'destroy'])->name('restaurante.waitlist.destroy')->middleware('permission:restaurante.cobrar');

    // KDS (Kitchen Display System)
    Route::get('/restaurante/kds', [KdsController::class, 'index'])->name('restaurante.kds.index')->middleware('permission:restaurante.view');
    Route::get('/restaurante/kds/orders', [KdsController::class, 'orders'])->name('restaurante.kds.orders')->middleware('permission:restaurante.view');
    Route::post('/restaurante/kds/update/{detalle}', [KdsController::class, 'updateEstado'])->name('restaurante.kds.update')->middleware('permission:restaurante.cobrar');
    Route::get('/restaurante/kds/audio', [KdsController::class, 'audio'])->name('restaurante.kds.audio')->middleware('permission:restaurante.view');
});

// Lavadero (Car Wash)
Route::middleware(['auth'])->group(function () {
    // Terminal POS
    Route::get('/lavadero', [\App\Http\Controllers\LavaderoController::class, 'index'])->name('lavadero.index')->middleware('permission:lavadero.view');
    Route::get('/lavadero/servicios', [\App\Http\Controllers\LavaderoServicioController::class, 'index'])->name('lavadero.servicios.index')->middleware('permission:lavadero.servicios');
    Route::post('/lavadero/servicios', [\App\Http\Controllers\LavaderoServicioController::class, 'store'])->name('lavadero.servicios.store')->middleware('permission:lavadero.servicios');
    Route::put('/lavadero/servicios/{servicio}', [\App\Http\Controllers\LavaderoServicioController::class, 'update'])->name('lavadero.servicios.update')->middleware('permission:lavadero.servicios');
    Route::delete('/lavadero/servicios/{servicio}', [\App\Http\Controllers\LavaderoServicioController::class, 'destroy'])->name('lavadero.servicios.destroy')->middleware('permission:lavadero.servicios');

    Route::get('/lavadero/vehiculos', [\App\Http\Controllers\VehiculoController::class, 'index'])->name('lavadero.vehiculos.index')->middleware('permission:lavadero.vehiculos');
    Route::get('/lavadero/vehiculos/{vehiculo}', [\App\Http\Controllers\VehiculoController::class, 'show'])->name('lavadero.vehiculos.show')->middleware('permission:lavadero.vehiculos');
    Route::put('/lavadero/vehiculos/{vehiculo}', [\App\Http\Controllers\VehiculoController::class, 'update'])->name('lavadero.vehiculos.update')->middleware('permission:lavadero.vehiculos');

    Route::get('/lavadero/citas', [\App\Http\Controllers\LavaderoCitaController::class, 'index'])->name('lavadero.citas.index')->middleware('permission:lavadero.citas');
    Route::post('/lavadero/citas', [\App\Http\Controllers\LavaderoCitaController::class, 'store'])->name('lavadero.citas.store')->middleware('permission:lavadero.citas');
    Route::put('/lavadero/citas/{cita}', [\App\Http\Controllers\LavaderoCitaController::class, 'update'])->name('lavadero.citas.update')->middleware('permission:lavadero.citas');
    Route::delete('/lavadero/citas/{cita}', [\App\Http\Controllers\LavaderoCitaController::class, 'destroy'])->name('lavadero.citas.destroy')->middleware('permission:lavadero.citas');

    // API endpoints (used by POS JS)
    Route::get('/lavadero/clientes', [\App\Http\Controllers\LavaderoController::class, 'buscarCliente'])->name('lavadero.clientes.buscar')->middleware('permission:lavadero.view');
    Route::post('/lavadero/clientes/crear', [\App\Http\Controllers\LavaderoController::class, 'createCliente'])->name('lavadero.clientes.crear')->middleware('permission:lavadero.view');
    Route::post('/lavadero/vehiculos/crear', [\App\Http\Controllers\LavaderoController::class, 'createVehiculo'])->name('lavadero.vehiculos.crear')->middleware('permission:lavadero.view');
    Route::get('/lavadero/vehiculos/{vehiculo}/historial', [\App\Http\Controllers\LavaderoController::class, 'historialVehiculo'])->name('lavadero.vehiculos.historial')->middleware('permission:lavadero.view');
    Route::get('/lavadero/servicios-json', [\App\Http\Controllers\LavaderoController::class, 'servicios'])->name('lavadero.servicios.json')->middleware('permission:lavadero.view');
    Route::post('/lavadero/cobrar', [\App\Http\Controllers\LavaderoController::class, 'cobrar'])->name('lavadero.cobrar')->middleware('permission:lavadero.view');
    Route::get('/lavadero/citas/hoy', [\App\Http\Controllers\LavaderoCitaController::class, 'hoy'])->name('lavadero.citas.hoy')->middleware('permission:lavadero.view');

    // Lavadores
    Route::get('/lavadero/lavadores', [\App\Http\Controllers\LavadorController::class, 'index'])->name('lavadero.lavadores.index')->middleware('permission:lavadero.lavadores');
    Route::post('/lavadero/lavadores', [\App\Http\Controllers\LavadorController::class, 'store'])->name('lavadero.lavadores.store')->middleware('permission:lavadero.lavadores');
    Route::put('/lavadero/lavadores/{lavador}', [\App\Http\Controllers\LavadorController::class, 'update'])->name('lavadero.lavadores.update')->middleware('permission:lavadero.lavadores');
    Route::delete('/lavadero/lavadores/{lavador}', [\App\Http\Controllers\LavadorController::class, 'destroy'])->name('lavadero.lavadores.destroy')->middleware('permission:lavadero.lavadores');
    Route::get('/lavadero/lavadores/activos', [\App\Http\Controllers\LavadorController::class, 'activos'])->name('lavadero.lavadores.activos')->middleware('permission:lavadero.view');
    Route::post('/lavadero/ventas/{venta}/lavadores', [\App\Http\Controllers\LavaderoController::class, 'asignarLavadores'])->name('lavadero.ventas.lavadores')->middleware('permission:lavadero.view');

    // Alquileres (Property Rentals)
    Route::get('/alquileres', [\App\Http\Controllers\AlquilerController::class, 'index'])->name('alquileres.index')->middleware('permission:alquileres.view');

    Route::get('/alquileres/viviendas', [\App\Http\Controllers\AlquilerViviendaController::class, 'index'])->name('alquileres.viviendas.index')->middleware('permission:alquileres.viviendas');
    Route::get('/alquileres/viviendas/crear', [\App\Http\Controllers\AlquilerViviendaController::class, 'create'])->name('alquileres.viviendas.create')->middleware('permission:alquileres.viviendas');
    Route::post('/alquileres/viviendas', [\App\Http\Controllers\AlquilerViviendaController::class, 'store'])->name('alquileres.viviendas.store')->middleware('permission:alquileres.viviendas');
    Route::get('/alquileres/viviendas/{vivienda}/editar', [\App\Http\Controllers\AlquilerViviendaController::class, 'edit'])->name('alquileres.viviendas.edit')->middleware('permission:alquileres.viviendas');
    Route::put('/alquileres/viviendas/{vivienda}', [\App\Http\Controllers\AlquilerViviendaController::class, 'update'])->name('alquileres.viviendas.update')->middleware('permission:alquileres.viviendas');
    Route::delete('/alquileres/viviendas/{vivienda}', [\App\Http\Controllers\AlquilerViviendaController::class, 'destroy'])->name('alquileres.viviendas.destroy')->middleware('permission:alquileres.viviendas');

    Route::get('/alquileres/inquilinos', [\App\Http\Controllers\AlquilerInquilinoController::class, 'index'])->name('alquileres.inquilinos.index')->middleware('permission:alquileres.inquilinos');
    Route::get('/alquileres/inquilinos/crear', [\App\Http\Controllers\AlquilerInquilinoController::class, 'create'])->name('alquileres.inquilinos.create')->middleware('permission:alquileres.inquilinos');
    Route::post('/alquileres/inquilinos', [\App\Http\Controllers\AlquilerInquilinoController::class, 'store'])->name('alquileres.inquilinos.store')->middleware('permission:alquileres.inquilinos');
    Route::get('/alquileres/inquilinos/{inquilino}/editar', [\App\Http\Controllers\AlquilerInquilinoController::class, 'edit'])->name('alquileres.inquilinos.edit')->middleware('permission:alquileres.inquilinos');
    Route::put('/alquileres/inquilinos/{inquilino}', [\App\Http\Controllers\AlquilerInquilinoController::class, 'update'])->name('alquileres.inquilinos.update')->middleware('permission:alquileres.inquilinos');
    Route::delete('/alquileres/inquilinos/{inquilino}', [\App\Http\Controllers\AlquilerInquilinoController::class, 'destroy'])->name('alquileres.inquilinos.destroy')->middleware('permission:alquileres.inquilinos');

    Route::get('/alquileres/contratos', [\App\Http\Controllers\AlquilerContratoController::class, 'index'])->name('alquileres.contratos.index')->middleware('permission:alquileres.contratos');
    Route::get('/alquileres/contratos/crear', [\App\Http\Controllers\AlquilerContratoController::class, 'create'])->name('alquileres.contratos.create')->middleware('permission:alquileres.contratos');
    Route::post('/alquileres/contratos', [\App\Http\Controllers\AlquilerContratoController::class, 'store'])->name('alquileres.contratos.store')->middleware('permission:alquileres.contratos');
    Route::get('/alquileres/contratos/{contrato}/editar', [\App\Http\Controllers\AlquilerContratoController::class, 'edit'])->name('alquileres.contratos.edit')->middleware('permission:alquileres.contratos');
    Route::put('/alquileres/contratos/{contrato}', [\App\Http\Controllers\AlquilerContratoController::class, 'update'])->name('alquileres.contratos.update')->middleware('permission:alquileres.contratos');
    Route::delete('/alquileres/contratos/{contrato}', [\App\Http\Controllers\AlquilerContratoController::class, 'destroy'])->name('alquileres.contratos.destroy')->middleware('permission:alquileres.contratos');

    Route::get('/alquileres/pagos', [\App\Http\Controllers\AlquilerPagoController::class, 'index'])->name('alquileres.pagos.index')->middleware('permission:alquileres.pagos');
    Route::get('/alquileres/pagos/crear', [\App\Http\Controllers\AlquilerPagoController::class, 'create'])->name('alquileres.pagos.create')->middleware('permission:alquileres.pagos');
    Route::post('/alquileres/pagos', [\App\Http\Controllers\AlquilerPagoController::class, 'store'])->name('alquileres.pagos.store')->middleware('permission:alquileres.pagos');
    Route::get('/alquileres/pagos/{pago}/editar', [\App\Http\Controllers\AlquilerPagoController::class, 'edit'])->name('alquileres.pagos.edit')->middleware('permission:alquileres.pagos');
    Route::put('/alquileres/pagos/{pago}', [\App\Http\Controllers\AlquilerPagoController::class, 'update'])->name('alquileres.pagos.update')->middleware('permission:alquileres.pagos');
    Route::delete('/alquileres/pagos/{pago}', [\App\Http\Controllers\AlquilerPagoController::class, 'destroy'])->name('alquileres.pagos.destroy')->middleware('permission:alquileres.pagos');
});

Route::get('/instancia-bloqueada', function () {
    return view('errors.instancia-bloqueada');
})->name('instancia-bloqueada');

// Setup Wizard
Route::middleware(['auth', 'instance.blocked'])->prefix('setup')->name('setup.')->group(function () {
    Route::get('/wizard', [\App\Http\Controllers\SetupWizardController::class, 'index'])->name('wizard');
    Route::post('/wizard/step', [\App\Http\Controllers\SetupWizardController::class, 'processStep'])->name('step');
    Route::post('/wizard/complete', [\App\Http\Controllers\SetupWizardController::class, 'complete'])->name('complete');
    Route::get('/wizard/restart', [\App\Http\Controllers\SetupWizardController::class, 'restart'])->name('restart');
    Route::get('/wizard/abrir-caja', [\App\Http\Controllers\SetupWizardController::class, 'abrirCaja'])->name('abrir-caja');
});

require __DIR__ . '/auth.php';
