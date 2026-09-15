#!/usr/bin/env php
<?php

/**
 * Manual column mapping for incompatible tables between backup and current schema
 * Safely inserts data by mapping only common columns, using defaults for new columns
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$backupFile = __DIR__ . '/storage/app/backups/backup_facturacion_db_20260914_163058.sql';

echo "================================================================================\n";
echo "  RESTAURACIÓN MANUAL - TABLAS CON ESQUEMA INCOMPATIBLE\n";
echo "  Backup: " . basename($backupFile) . "\n";
echo "  Fecha:  " . date('Y-m-d H:i:s') . "\n";
echo "  ⚠️  Solo INSERT de columnas compatibles, defaults para nuevas\n";
echo "================================================================================\n\n";

// Parse backup to extract INSERT statements for specific tables
$targetTables = [
    'business_instances',  // Must be first - referenced by many tables
    'categorias',          // Referenced by productos
    'tipos_ventas',        // Referenced by ventas
    'almacenes',           // Referenced by almacen_movimientos
    'sucursales',          // Referenced by ventas
    'cajas',               // Referenced by ventas
    'clientes',            // Referenced by ventas
    'productos',           // Referenced by ventas, venta_detalles, almacen_movimientos
    'ventas',              // Referenced by venta_detalles
    'venta_detalles',
    'mesas',
    'almacen_movimientos',
    'system_settings',
    'instance_api_keys',
];

$inserts = [];
$handle = fopen($backupFile, 'r');
$buffer = '';
$inInsert = false;
$currentTable = null;

while ($line = fgets($handle)) {
    $trimmed = trim($line);
    
    // Skip non-data lines
    if (empty($trimmed) || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '/*!') || str_starts_with($trimmed, 'SET') || str_starts_with($trimmed, 'LOCK') || str_starts_with($trimmed, 'UNLOCK') || str_starts_with($trimmed, 'DROP') || str_starts_with($trimmed, 'CREATE') || str_starts_with($trimmed, '/*')) {
        continue;
    }
    
    // Match INSERT INTO `tablename` VALUES ...
    if (preg_match('/^INSERT INTO `(\w+)`\s+VALUES\s+\((.+)$/s', $trimmed, $m)) {
        $table = $m[1];
        if (!in_array($table, $targetTables)) continue;
        
        // If we were already in an insert, something went wrong
        if ($inInsert) {
            echo "   WARNING: Previous INSERT for $currentTable was not closed before starting $table\n";
        }
        
        $currentTable = $table;
        $buffer = $m[2];
        $inInsert = true;
        
        // Check if complete on this line
        if (str_ends_with($trimmed, ');')) {
            $buffer = rtrim($buffer, "; \n\r\t");
            $inserts[$table] = ['values' => $buffer];
            echo "   DEBUG: Stored INSERT for $table, values length: " . strlen($buffer) . ", first 80: " . substr($buffer, 0, 80) . "\n";
            $inInsert = false;
            $currentTable = null;
        }
        continue;
    }
    
    if ($inInsert && $currentTable) {
        $buffer .= ' ' . $trimmed;
        if (str_ends_with($trimmed, ');')) {
            $buffer = rtrim($buffer, "; \n\r\t");
            $inserts[$currentTable] = ['values' => $buffer];
            echo "   DEBUG: Stored multi-line INSERT for $currentTable, values length: " . strlen($buffer) . "\n";
            $inInsert = false;
            $currentTable = null;
        }
    }
}
fclose($handle);

echo "Tablas con datos en backup: " . count($inserts) . "\n\n";

// Get current table columns from DB
$currentColumns = [];
foreach ($targetTables as $table) {
    if (Schema::hasTable($table)) {
        $cols = DB::select("DESCRIBE `$table`");
        $currentColumns[$table] = array_column($cols, 'Field');
    } else {
        $currentColumns[$table] = [];
    }
}

// Column mappings: backup column index -> current column name
// Based on analyzing both schemas
$columnMappings = [
    'categorias' => [
        // Backup: id, tenant_id, nombre, descripcion, activa, color, icono, orden, configuracion, deleted_at, created_at, updated_at (12 cols)
        // Current: id, tenant_id, nombre, descripcion, activa, created_at, updated_at (7 cols)
        'backup_cols' => ['id', 'tenant_id', 'nombre', 'descripcion', 'activa', 'color', 'icono', 'orden', 'configuracion', 'deleted_at', 'created_at', 'updated_at'],
        'current_cols' => ['id', 'tenant_id', 'nombre', 'descripcion', 'activa', 'created_at', 'updated_at'],
        'skip_indices' => [5, 6, 7, 8, 9], // skip color, icono, orden, configuracion, deleted_at
    ],
    'clientes' => [
        // Backup: 33 cols (no deleted_at)
        // Current: 36 cols (added deleted_at, whatsapp, acceso_api, password, email_verified_at, remember_token)
        // Backup has: id, tenant_id, nombre, rnc, email, telefono, whatsapp, direccion, ciudad, provincia, codigo_postal,
        // segmento, origen_cliente, sector_actividad, persona_contacto, cargo_contacto, rnc_cedula, tipo_documento,
        // tipo_cliente, regimen_mensual, nit, limite_credito, balance_pendiente, plazo_pago_dias, tasa_descuento_pct,
        // moneda, auto_bloquear_credito, notas_internas, activo, created_at, updated_at
        'backup_cols' => ['id', 'tenant_id', 'nombre', 'rnc', 'email', 'telefono', 'whatsapp', 'direccion', 'ciudad', 'provincia', 'codigo_postal',
            'segmento', 'origen_cliente', 'sector_actividad', 'persona_contacto', 'cargo_contacto', 'rnc_cedula', 'tipo_documento',
            'tipo_cliente', 'regimen_mensual', 'nit', 'limite_credito', 'balance_pendiente', 'plazo_pago_dias', 'tasa_descuento_pct',
            'moneda', 'auto_bloquear_credito', 'notas_internas', 'activo', 'acceso_api', 'password', 'email_verified_at', 'remember_token',
            'created_at', 'updated_at'],
        'current_cols' => ['id', 'tenant_id', 'nombre', 'rnc', 'email', 'telefono', 'whatsapp', 'direccion', 'ciudad', 'provincia', 'codigo_postal',
            'segmento', 'origen_cliente', 'sector_actividad', 'persona_contacto', 'cargo_contacto', 'rnc_cedula', 'tipo_documento',
            'tipo_cliente', 'regimen_mensual', 'nit', 'limite_credito', 'balance_pendiente', 'plazo_pago_dias', 'tasa_descuento_pct',
            'moneda', 'auto_bloquear_credito', 'notas_internas', 'activo', 'acceso_api', 'password', 'email_verified_at', 'remember_token',
            'created_at', 'updated_at', 'deleted_at'],
        // Map all backup cols to current (current has extra deleted_at at end)
    ],
'productos' => [
        // Backup columns (from CREATE TABLE): 55 cols (indices 0-54)
        // Current: 37 cols
        'backup_cols' => ['id', 'tenant_id', 'nombre', 'codigo_barras', 'codigo_referencia', 'descripcion', 'precio', 'precio_compra', 'unidad_medida', 'itbis_porcentaje', 'stock', 'tiene_almacen', 'ventas_count', 'stock_minimo', 'activo', 'is_art_piece', 'incluir_kds', 'imagen', 'created_at', 'updated_at', 'categoria_id', 'category_subcategory_id', 'product_type', 'requiere_serial', 'serial_imei', 'categoria_tecnica', 'es_licencia', 'tipo_licencia', 'licencia_max_usuarios', 'requires_setup', 'marca_tecnologica_id', 'especializacion', 'vendible_imei', 'requiere_imei', 'marca', 'modelo', 'capacidad_toneladas', 'capacidad_btu', 'tipo_equipo', 'eficiencia_seer', 'gas_refrigerante', 'voltaje', 'peso_kg', 'dimensiones', 'categoria_clima', 'tipo_servicio', 'tipo_producto', 'linea_negocio', 'almacenamiento_gb', 'color', 'precio_servicio', 'duracion_servicio_horas', 'garantia_dias', 'garantia_meses', 'garantia_terminos'],
        'current_cols' => ['id', 'tenant_id', 'nombre', 'codigo_barras', 'descripcion', 'precio', 'precio_compra', 'unidad_medida', 'itbis_porcentaje', 'stock', 'ventas_count', 'stock_minimo', 'activo', 'imagen', 'created_at', 'updated_at', 'categoria_id', 'especializacion', 'vendible_imei', 'requiere_imei', 'marca', 'modelo', 'capacidad_toneladas', 'capacidad_btu', 'tipo_equipo', 'eficiencia_seer', 'gas_refrigerante', 'voltaje', 'peso_kg', 'dimensiones', 'categoria_clima', 'almacenamiento_gb', 'color', 'precio_servicio', 'duracion_servicio_horas', 'garantia_dias', 'deleted_at'],
        // Map by backup column index -> current column name
        'index_map' => [
            0 => 'id', 1 => 'tenant_id', 2 => 'nombre', 3 => 'codigo_barras', 6 => 'precio', 7 => 'precio_compra',
            8 => 'unidad_medida', 9 => 'itbis_porcentaje', 10 => 'stock', 12 => 'ventas_count', 13 => 'stock_minimo',
            14 => 'activo', 17 => 'imagen', 18 => 'created_at', 19 => 'updated_at', 20 => 'categoria_id',
            31 => 'especializacion', 32 => 'vendible_imei', 33 => 'requiere_imei', 34 => 'marca', 35 => 'modelo',
            36 => 'capacidad_toneladas', 37 => 'capacidad_btu', 38 => 'tipo_equipo', 39 => 'eficiencia_seer',
            40 => 'gas_refrigerante', 41 => 'voltaje', 42 => 'peso_kg', 43 => 'dimensiones', 44 => 'categoria_clima',
            48 => 'almacenamiento_gb', 49 => 'color', 50 => 'precio_servicio', 51 => 'duracion_servicio_horas',
            52 => 'garantia_dias',
        ],
    ],
    'ventas' => [
        // Backup: 42 cols
        // Current: 36 cols
        'backup_cols' => ['id', 'tenant_id', 'retenciones', 'sucursal_id', 'template_id', 'mesa_id', 'vehiculo_id', 'ncf', 'ncf_tipo', 'tipo_comprobante', 'encf', 'ncf_vencimiento', 'user_id', 'caja_id', 'sesion_caja_id', 'lista_precio_id', 'cliente_id', 'tipo_venta_id', 'fecha', 'subtotal', 'impuestos', 'descuento', 'general_descuento', 'propina', 'cargo_servicio', 'descuento_tipo', 'descuento_motivo', 'notas', 'retencion_isr', 'retencion_itbis', 'total', 'ventas_count', 'estado', 'tipo_orden', 'delivery_company_id', 'delivery_fee', 'driver_id', 'delivery_address', 'delivery_zone_id', 'distancia_km', 'tarifa_delivery', 'created_at', 'updated_at', 'deleted_at'],
        'current_cols' => ['id', 'tenant_id', 'sucursal_id', 'mesa_id', 'vehiculo_id', 'ncf', 'ncf_tipo', 'tipo_comprobante', 'encf', 'ncf_vencimiento', 'user_id', 'caja_id', 'sesion_caja_id', 'lista_precio_id', 'cliente_id', 'tipo_venta_id', 'fecha', 'subtotal', 'impuestos', 'descuento', 'propina', 'cargo_servicio', 'descuento_tipo', 'descuento_motivo', 'notas', 'retencion_isr', 'retencion_itbis', 'total', 'ventas_count', 'estado', 'tipo_orden', 'delivery_company_id', 'delivery_fee', 'created_at', 'updated_at', 'deleted_at'],
        'field_map' => [
            'id' => 'id', 'tenant_id' => 'tenant_id', 'sucursal_id' => 'sucursal_id', 'mesa_id' => 'mesa_id',
            'vehiculo_id' => 'vehiculo_id', 'ncf' => 'ncf', 'ncf_tipo' => 'ncf_tipo', 'tipo_comprobante' => 'tipo_comprobante',
            'encf' => 'encf', 'ncf_vencimiento' => 'ncf_vencimiento', 'user_id' => 'user_id', 'caja_id' => 'caja_id',
            'sesion_caja_id' => 'sesion_caja_id', 'lista_precio_id' => 'lista_precio_id', 'cliente_id' => 'cliente_id',
            'tipo_venta_id' => 'tipo_venta_id', 'fecha' => 'fecha', 'subtotal' => 'subtotal', 'impuestos' => 'impuestos',
            'descuento' => 'descuento', 'propina' => 'propina', 'cargo_servicio' => 'cargo_servicio',
            'descuento_tipo' => 'descuento_tipo', 'descuento_motivo' => 'descuento_motivo', 'notas' => 'notas',
            'retencion_isr' => 'retencion_isr', 'retencion_itbis' => 'retencion_itbis', 'total' => 'total',
            'ventas_count' => 'ventas_count', 'estado' => 'estado', 'tipo_orden' => 'tipo_orden',
            'delivery_company_id' => 'delivery_company_id', 'delivery_fee' => 'delivery_fee',
            'created_at' => 'created_at', 'updated_at' => 'updated_at', 'deleted_at' => 'deleted_at',
        ],
    ],
    'venta_detalles' => [
        // Backup: 20 cols
        // Current: 14 cols (added lavador_id, removed several)
        'backup_cols' => ['id', 'venta_id', 'producto_id', 'almacen_id', 'cantidad', 'precio_unitario', 'subtotal', 'descuento', 'descuento_tipo', 'itbis_porcentaje', 'sin_itbis', 'tipo_linea', 'notas', 'estado_cocina', 'cocina_updated_at', 'curso', 'created_at', 'updated_at', 'tenant_id', 'lavador_id'],
        'current_cols' => ['id', 'venta_id', 'producto_id', 'almacen_id', 'cantidad', 'precio_unitario', 'subtotal', 'notas', 'estado_cocina', 'cocina_updated_at', 'curso', 'created_at', 'updated_at', 'tenant_id'],
        'field_map' => [
            'id' => 'id', 'venta_id' => 'venta_id', 'producto_id' => 'producto_id', 'almacen_id' => 'almacen_id',
            'cantidad' => 'cantidad', 'precio_unitario' => 'precio_unitario', 'subtotal' => 'subtotal',
            'notas' => 'notas', 'estado_cocina' => 'estado_cocina', 'cocina_updated_at' => 'cocina_updated_at',
            'curso' => 'curso', 'created_at' => 'created_at', 'updated_at' => 'updated_at', 'tenant_id' => 'tenant_id',
        ],
    ],
    'mesas' => [
        // Backup: 15 cols (id, sucursal_id, numero, nombre, icono, capacidad, ubicacion_id, estado, activa, pos_x, pos_y, created_at, updated_at, categoria_id, tenant_id)
        // Current: 14 cols (no icono)
        'backup_cols' => ['id', 'sucursal_id', 'numero', 'nombre', 'icono', 'capacidad', 'ubicacion_id', 'estado', 'activa', 'pos_x', 'pos_y', 'created_at', 'updated_at', 'categoria_id', 'tenant_id'],
        'current_cols' => ['id', 'sucursal_id', 'numero', 'nombre', 'capacidad', 'ubicacion_id', 'estado', 'activa', 'pos_x', 'pos_y', 'created_at', 'updated_at', 'categoria_id', 'tenant_id'],
        'skip_indices' => [4], // skip icono
    ],
    'almacenes' => [
        // Backup: 7 cols (id, sucursal_id, nombre, ubicacion, created_at, updated_at, tenant_id)
        // Current: 7 cols (same)
        'backup_cols' => ['id', 'sucursal_id', 'nombre', 'ubicacion', 'created_at', 'updated_at', 'tenant_id'],
        'current_cols' => ['id', 'sucursal_id', 'nombre', 'ubicacion', 'created_at', 'updated_at', 'tenant_id'],
    ],
    'sucursales' => [
        // Backup: 13 cols (id, codigo, nombre, direccion, telefono, email, rnc, activa, es_matriz, created_at, updated_at, deleted_at, tenant_id)
        // Current: 13 cols (same)
        'backup_cols' => ['id', 'codigo', 'nombre', 'direccion', 'telefono', 'email', 'rnc', 'activa', 'es_matriz', 'created_at', 'updated_at', 'deleted_at', 'tenant_id'],
        'current_cols' => ['id', 'codigo', 'nombre', 'direccion', 'telefono', 'email', 'rnc', 'activa', 'es_matriz', 'created_at', 'updated_at', 'deleted_at', 'tenant_id'],
    ],
    'cajas' => [
        // Backup: 12 cols (id, sucursal_id, nombre, codigo, ubicacion, activo, allowed_comprobante_types, estado, created_at, updated_at, tenant_id)
        // Current: 10 cols (no codigo, allowed_comprobante_types, estado)
        'backup_cols' => ['id', 'sucursal_id', 'nombre', 'codigo', 'ubicacion', 'activo', 'allowed_comprobante_types', 'estado', 'created_at', 'updated_at', 'tenant_id'],
        'current_cols' => ['id', 'sucursal_id', 'nombre', 'ubicacion', 'activo', 'created_at', 'updated_at', 'tenant_id'],
        'skip_indices' => [3, 6, 7], // skip codigo, allowed_comprobante_types, estado
    ],
    'tipos_ventas' => [
        // Backup: 5 cols (id, nombre, descripcion, created_at, updated_at)
        // Current: 6 cols (added tenant_id)
        'backup_cols' => ['id', 'nombre', 'descripcion', 'created_at', 'updated_at'],
        'current_cols' => ['id', 'nombre', 'descripcion', 'created_at', 'updated_at', 'tenant_id'],
        'defaults' => ['tenant_id' => 1],
    ],
    'almacen_movimientos' => [
        // Backup: 11 cols
        // Current: 11 cols (same)
        'backup_cols' => ['id', 'producto_id', 'detalle_compra_id', 'user_id', 'almacen_id', 'tipo', 'cantidad', 'nota', 'created_at', 'updated_at', 'tenant_id'],
        'current_cols' => ['id', 'producto_id', 'detalle_compra_id', 'user_id', 'almacen_id', 'tipo', 'cantidad', 'nota', 'created_at', 'updated_at', 'tenant_id'],
    ],
    'business_instances' => [
        // Backup: 29 cols
        // Current: check actual
        'backup_cols' => ['id', 'nombre', 'slug', 'rnc', 'email', 'telefono', 'direccion', 'logo', 'business_type_id', 'plan_id', 'owner_user_id', 'owner_email', 'owner_nombre', 'configuracion', 'costo_mensual', 'bloqueado', 'setup_completed', 'aprobado', 'aprobado_en', 'rechazo_motivo', 'deleted_at', 'motivo_bloqueo', 'bloqueado_en', 'activo', 'trial_ends_at', 'trial_started_at', 'fecha_vencimiento', 'created_at', 'updated_at'],
        'current_cols' => [], // Will fetch from DB
    ],
    'system_settings' => [
        // Backup: 9 cols
        // Current: check actual
        'backup_cols' => ['id', 'clave', 'grupo', 'valor', 'tipo', 'descripcion', 'created_at', 'updated_at', 'tenant_id'],
        'current_cols' => [],
    ],
    'instance_api_keys' => [
        // Backup: 11 cols
        // Current: 9 cols (no key_raw, no deleted_at)
        'backup_cols' => ['id', 'business_instance_id', 'name', 'key', 'key_raw', 'last_used_at', 'is_active', 'created_by', 'created_at', 'updated_at', 'deleted_at'],
        'current_cols' => ['id', 'business_instance_id', 'name', 'key', 'last_used_at', 'is_active', 'created_by', 'created_at', 'updated_at'],
        'skip_indices' => [4, 10], // skip key_raw, deleted_at
    ],
];

// Fetch current columns for business_instances and system_settings
if (Schema::hasTable('business_instances')) {
    $cols = DB::select("DESCRIBE `business_instances`");
    $columnMappings['business_instances']['current_cols'] = array_column($cols, 'Field');
}
if (Schema::hasTable('system_settings')) {
    $cols = DB::select("DESCRIBE `system_settings`");
    $columnMappings['system_settings']['current_cols'] = array_column($cols, 'Field');
}

echo "Esquemas actuales:\n";
foreach ($targetTables as $t) {
    if (isset($currentColumns[$t])) {
        echo "  $t: " . count($currentColumns[$t]) . " columnas\n";
    }
}
echo "\n";

// Process each table
$results = [];
$pdo = DB::connection()->getPdo();

foreach ($targetTables as $table) {
    if (!isset($inserts[$table])) {
        echo "⏭️  $table: No hay datos en backup\n";
        continue;
    }
    
    if (!isset($columnMappings[$table])) {
        echo "⏭️  $table: Sin mapeo definido\n";
        continue;
    }
    
    $mapping = $columnMappings[$table];
    $backupCols = $mapping['backup_cols'] ?? [];
    $currentCols = $mapping['current_cols'] ?: ($currentColumns[$table] ?? []);
    $fieldMap = $mapping['field_map'] ?? [];
    $indexMap = $mapping['index_map'] ?? [];
    $defaults = $mapping['defaults'] ?? [];
    $skipIndices = $mapping['skip_indices'] ?? [];
    
    if (empty($backupCols) || empty($currentCols)) {
        echo "⏭️  $table: Esquema no disponible\n";
        continue;
    }
    
    // Build column list for INSERT
    $insertCols = [];
    $colIndices = []; // backup index -> current column name
    
    if (!empty($indexMap)) {
        // Index-based mapping (backup column index -> current column name)
        foreach ($indexMap as $backupIdx => $currentField) {
            if (in_array($currentField, $currentCols) && $backupIdx < count($backupCols)) {
                $insertCols[] = "`$currentField`";
                $colIndices[] = $backupIdx;
            }
        }
    } elseif (!empty($fieldMap)) {
        // Explicit field mapping by name
        foreach ($fieldMap as $backupField => $currentField) {
            if (in_array($currentField, $currentCols)) {
                $backupIdx = array_search($backupField, $backupCols);
                if ($backupIdx !== false) {
                    $insertCols[] = "`$currentField`";
                    $colIndices[] = $backupIdx;
                }
            }
        }
    } else {
        // Direct mapping by position (skip indices)
        for ($i = 0; $i < count($backupCols); $i++) {
            if (in_array($i, $skipIndices)) continue;
            if (isset($currentCols[$i])) {
                $insertCols[] = "`{$currentCols[$i]}`";
                $colIndices[] = $i;
            }
        }
    }
    
    // DEBUG: Show colIndices for this table
    if ($table === 'productos') {
        echo "   DEBUG colIndices for productos: " . implode(', ', $colIndices) . "\n";
        echo "   DEBUG insertCols for productos: " . implode(', ', $insertCols) . "\n";
    }
    
    // Add defaults as columns
    foreach ($defaults as $col => $val) {
        if (in_array($col, $currentCols) && !in_array($col, $insertCols)) {
            $insertCols[] = "`$col`";
            $colIndices[] = 'DEFAULT:' . $val;
        }
    }
    
    if (empty($insertCols)) {
        echo "⏭️  $table: No hay columnas compatibles\n";
        continue;
    }
    
    // Parse values from backup
    $valuesStr = $inserts[$table]['values'];
    // The regex captures values WITHOUT the opening paren, so prepend it
    if ($valuesStr[0] !== '(') {
        $valuesStr = '(' . $valuesStr;
    }
    $tuples = parseTuples($valuesStr);
    
    // DEBUG: Show first tuple parsed fields
    if (!empty($tuples)) {
        echo "   DEBUG first tuple fields: " . count($tuples[0]['fields']) . " fields\n";
        foreach ($tuples[0]['fields'] as $idx => $f) {
            if ($idx < 5 || $idx === 1) {
                echo "     [$idx] = $f\n";
            }
        }
        // DEBUG: Show raw first tuple
        echo "   DEBUG raw first tuple (first 200 chars): " . substr($valuesStr, 0, 200) . "\n";
    }
    
    if (empty($tuples)) {
        echo "⏭️  $table: No se pudieron parsear tuplas\n";
        continue;
    }
    
    echo "🔄 $table: {$tuples[0]['count']} campos por tupla, " . count($tuples) . " registros\n";
    echo "   Columnas a insertar: " . implode(', ', $insertCols) . "\n";
    
    // Build and execute INSERT IGNORE
    $inserted = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($tuples as $tuple) {
        $values = [];
        foreach ($colIndices as $idx) {
            if (is_string($idx) && str_starts_with($idx, 'DEFAULT:')) {
                $values[] = substr($idx, 8);
            } elseif (is_int($idx) && isset($tuple['fields'][$idx])) {
                $val = $tuple['fields'][$idx];
                // Escape for SQL
                $val = str_replace("'", "''", $val);
                $values[] = $val === 'NULL' ? 'NULL' : "'$val'";
            } else {
                $values[] = 'NULL';
            }
        }
        
        // DEBUG: Show first tuple SQL
        if ($inserted === 0 && $skipped === 0 && $errors === 0) {
            $tenantIdIdx = array_search('`tenant_id`', $insertCols);
            if ($tenantIdIdx !== false) {
                echo "   DEBUG tenant_id value: " . $values[$tenantIdIdx] . "\n";
            }
            $debugSql = "INSERT IGNORE INTO `$table` (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $values) . ")";
            echo "   DEBUG SQL: " . substr($debugSql, 0, 300) . "...\n";
        }
        
        $sql = "INSERT IGNORE INTO `$table` (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', $values) . ")";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $inserted++;
            } else {
                $skipped++;
            }
        } catch (PDOException $e) {
            $errors++;
            if ($errors <= 3) {
                echo "   ✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    $results[$table] = ['inserted' => $inserted, 'skipped' => $skipped, 'errors' => $errors];
    echo "   ✓ Insertados: $inserted, Duplicados omitidos: $skipped, Errores: $errors\n\n";
}

echo "================================================================================\n";
echo "  RESUMEN FINAL\n";
echo "================================================================================\n";
foreach ($results as $table => $r) {
    echo "  $table: {$r['inserted']} nuevos, {$r['skipped']} duplicados, {$r['errors']} errores\n";
}
echo "\n✅ Completado\n";

function parseTuples($valuesStr) {
    $tuples = [];
    $depth = 0;
    $current = '';
    $inQuote = false;
    $quoteChar = '';
    
    for ($i = 0; $i < strlen($valuesStr); $i++) {
        $c = $valuesStr[$i];
        
        if (($c === "'" || $c === '"') && !$inQuote) {
            $inQuote = true;
            $quoteChar = $c;
            $current .= $c;
        } elseif ($c === $quoteChar && $inQuote) {
            $inQuote = false;
            $quoteChar = '';
            $current .= $c;
        } elseif ($c === '(' && !$inQuote) {
            if ($depth === 0) {
                $current = '';
            } else {
                $current .= $c;
            }
            $depth++;
        } elseif ($c === ')' && !$inQuote) {
            $depth--;
            if ($depth === 0) {
                // End of tuple
                $fields = parseFields($current);
                $tuples[] = ['fields' => $fields, 'count' => count($fields)];
                $current = '';
            } else {
                $current .= $c;
            }
        } else {
            $current .= $c;
        }
    }
    return $tuples;
}

function parseFields($tupleStr) {
    $fields = [];
    $current = '';
    $inQuote = false;
    $quoteChar = '';
    
    for ($i = 0; $i < strlen($tupleStr); $i++) {
        $c = $tupleStr[$i];
        
        if (($c === "'" || $c === '"') && !$inQuote) {
            $inQuote = true;
            $quoteChar = $c;
            $current .= $c;
        } elseif ($c === $quoteChar && $inQuote) {
            $inQuote = false;
            $quoteChar = '';
            $current .= $c;
        } elseif ($c === ',' && !$inQuote) {
            $fields[] = trim($current);
            $current = '';
        } else {
            $current .= $c;
        }
    }
    if ($current !== '') {
        $fields[] = trim($current);
    }
    return $fields;
}