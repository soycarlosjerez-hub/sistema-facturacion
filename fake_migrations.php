<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Migrations that are "pending" but already have the columns applied in DB
// We insert them into the migrations table to skip them
$toFake = [
    '2026_06_24_000006_add_tenant_id_to_almacenes_pagos_cajas',
    '2026_06_24_232120_backfill_tenant_id_to_almacenes',
    '2026_06_25_000001_add_tenant_id_to_proveedores_gastos_cotizaciones_conduces_devoluciones_sucursales',
    '2026_06_25_000002_add_tenant_id_to_venta_detalles_compra_detalles_ecf_mesa_lavadero',
    '2026_06_25_190331_backfill_tenant_id_to_cajas',
    '2026_06_26_000001_add_tenant_id_to_conduce_items_cotizacion_items_ecf_log_envios',
    '2026_06_26_000002_add_tenant_id_to_lista_precios_mesa_categorias_split_bill_waitlist',
];

// Get max batch
$maxBatch = DB::table('migrations')->max('batch') ?? 0;

foreach ($toFake as $migration) {
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    if (!$exists) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch'     => $maxBatch + 1,
        ]);
        echo "Faked: $migration\n";
    } else {
        echo "Already exists: $migration\n";
    }
}

echo "\nDone. Now run: php artisan migrate\n";
