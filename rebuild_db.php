<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Drop all app tables
DB::statement('SET FOREIGN_KEY_CHECKS = 0');

$tables = [
    'business_instances',
    'business_instance_modules',
    'business_types',
    'cache',
    'cache_locks',
    'categorias',
    'categorias_lavadero_servicios',
    'certificados_autenticidad',
    'certificados_digitales',
    'clientes',
    'compra_detalles',
    'compras',
    'conduces',
    'conduce_items',
    'cotizaciones',
    'cotizacion_items',
    'ecf_documentos',
    'ecf_log_envios',
    'equipos',
    'exhibiciones',
    'failed_jobs',
    'gastos',
    'garantias',
    'instalar_equipo_domotico',
    'instalaciones',
    'instalacion_productos',
    'ingredientes',
    'lista_precios',
    'lista_precio_logs',
    'lista_precio_productos',
    'mantenimientos',
    'mesas',
    'mesa_categorias',
    'mesa_ubicaciones',
    'model_has_permissions',
    'model_has_roles',
    'ordenes',
    'orden_detalles',
    'orden_estado_historial',
    'pagos',
    'pagos_de_instancia',
    'password_reset_tokens',
    'permissions',
    'productos',
    'producto_ingrediente',
  'proveedores',
    'role_has_permissions',
    'roles',
    'secuencias_ecf',
    'sessions',
    'sesion_cajas',
    'tecnicos',
    'tipos_ventas',
    'usuarios',
    'users',
    'ventas',
    'waiting_list',
    'wizard_steps',
];

foreach ($tables as $table) {
    try {
        DB::statement('DROP TABLE IF EXISTS `' . $table . '`');
        echo "Dropped $table\n";
    } catch (Exception $e) {
        echo "Error dropping $table: " . $e->getMessage() . "\n";
    }
}

// Recreate users table and other base tables
echo "\nRunning migrations starting from base...\n";
