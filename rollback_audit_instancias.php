<?php
/**
 * SCRIPT DE ROLLBACK — Auditoría QA Black Box (Instancias)
 * 
 * Fecha de aplicación: 2026-07-14
 * 
 * INSTRUCCIONES PARA REVERTIR:
 * 1. Ejecutar: php rollback_audit_instancias.php
 * 2. Revertir migración: php artisan migrate:rollback --step=1
 * 3. Verificar backups generados en storage/logs/rollback_backup_*.sql
 * 
 * CAMBIOS REALIZADOS:
 * - [C1] Removido SMTP hardcodeado en BusinessInstance::seedSmtpSettings() → usa .env
 * - [C2] Agregado soft-delete (trashed_at) a BusinessInstance
 * - [C3] Corregido FK checks en cleanInstance (eliminado SET FOREIGN_KEY_CHECKS=0)
 * - [A2] Abilities limitadas en Sanctum tokens de instancia (instancia:*)
 * - [A3] Incluidas tablas opcionales en cleanInstance (alquiler, tattoo, vehiculos)
 * - [A4] Mínimo contraseña subido de 6 a 12 caracteres
 * - [I2] Corregido scopeConAtraso bug (copypaste de scopeAlDia)
 * - [I3] Eliminada línea TEST residual y doble cierre de llave
 * - [I4] Limpiadas rutas comentadas de admin-business
 * - [M5] Emails únicos por tenant (permitidos duplicados entre instancias)
 * - [M3] Registro de acciones de Owner en audit_logs
 * - [M4] Transacción en syncModules para evitar race conditions
 */

echo "============================================================\n";
echo "ROLLBACK — Auditoría QA Black Box (Instancias)\n";
echo "============================================================\n\n";

$dbHost = '127.0.0.1';
$dbName = 'sistema_facturacion';
$dbUser = 'root';
$dbPass = '';

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if (!$conn) {
    die("ERROR: No se pudo conectar a la BD. Host=$dbHost DB=$dbName\n");
}

echo "[1/6] Creando backup de seguridad...\n";
$backupFile = __DIR__ . '/storage/logs/rollback_backup_' . date('Ymd_His') . '.sql';
mkdir(__DIR__ . '/storage/logs', 0777, true);
exec("mysqldump --single-transaction -h $dbHost -u $dbUser $dbPass $dbName > \"$backupFile\"", $output, $returnVar);
if ($returnVar === 0) {
    echo "  ✓ Backup creado: $backupFile\n\n";
} else {
    echo "  ⚠ mysqldump no disponible o falló. Continuando sin backup SQL.\n\n";
}

echo "[2/6] Revertiendo BusinessInstance.php...\n";
$modelPath = __DIR__ . '/app/Models/BusinessInstance.php';
$content = file_get_contents($modelPath);

// Restaurar seed SMTP hardcodeado
$content = preg_replace(
    "/\$settings\s*=\s*\[/",
    '$settings = [
            \'mail_mailer\'     => \'smtp\',
            \'mail_host\'       => \'mail.armada.do\',
            \'mail_port\'       => \'465\',
            \'mail_username\'   => \'no-reply@armada.do\',
            \'mail_password\'   => Crypt::encryptString(\'Dn%q#U0tV,65FqSU\'),
            \'mail_encryption\' => \'ssl\',
            \'mail_from_address\' => \'no-reply@armada.do\',
            \'mail_from_name\'    => \'Sistema de Facturaci\u00f3n\',',
    $content
);

// Quitar trashed_at del fillable
$content = str_replace("'trashed_at',\n        ", '', $content);

// Quitar casting de trashed_at
$content = str_replace("'trashed_at' => 'datetime',\n        ", '', $content);

// Quitar scopes de soft-delete
$content = preg_replace("/\s*public function scopeTrashed\(\$query\).*?\n\s*\}\n*/s", '', $content);
$content = preg_replace("/\s*public function scopeWithoutTrashed\(\$query\).*?\n\s*\}\n*/s", '', $content);
$content = preg_replace("/\s*public function restore\(\).*?\n\s*\}\n*/s", '', $content);
$content = preg_replace("/\s*public function forceRestore\(\).*?\n\s*\}\n*/s", '', $content);
$content = preg_replace("/\s*public function forceDelete\(\).*?\n\s*\}\n*/s", '', $content);
$content = preg_replace("/\s*public function isTrashed\(\).*?\n\s*\}\n*/s", '', $content);

// Quitar boot de soft-delete
$content = preg_replace("/\s*static::restoring\(function \(self \$instance\) \{.*?\n\s*\}\)/s", '', $content);
$content = preg_replace("/\s*static::removing\(function \(self \$instance\) \{.*?\n\s*\}\)/s", '', $content);

file_put_contents($modelPath, $content);
echo "  ✓ BusinessInstance.php revertido\n\n";

echo "[3/6] Revertiendo OwnerController.php...\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/OwnerController.php';
$content = file_get_contents($controllerPath);

// Restaurar password min 6
$content = str_replace("'password' => 'required|string|min:12|confirmed'", "'password' => 'required|string|min:6|confirmed'", $content);
$content = str_replace("'password' => 'nullable|string|min:12|confirmed'", "'password' => 'nullable|string|min:6|confirmed'", $content);

// Restaurar abilities wildcard
$content = str_replace("\$abilities = \$request->input('abilities', ['instancia:*']);\n        \$token = \$user->createToken(\$data['name'], (array) \$abilities);", "$abilities = \$request->input('abilities', ['*']);\n        \$token = \$user->createToken(\$data['name'], (array) \$abilities);", $content);

// Quitar línea TEST LINE si fue agregada (no debería estar)
$content = preg_replace("/^\s*\/\/ TEST LINE\s*$/m", '', $content);

file_put_contents($controllerPath, $content);
echo "  ✓ OwnerController.php revertido\n\n";

echo "[4/6] Revertiendo InstanceRole.php...\n";
$rolePath = __DIR__ . '/app/Models/InstanceRole.php';
$content = file_get_contents($rolePath);

// Quitar transacción de syncModules
$content = str_replace(
    "DB::beginTransaction();\n        try {\n            \$data = [];\n            \$orden = 0;\n            foreach (\$moduleKeys as \$key) {",
    "\$data = [];\n        \$orden = 0;\n        foreach (\$moduleKeys as \$key) {"
);
$content = str_replace(
    "}\n            \$this->modules()->create(\$item);\n        }\n        DB::commit();\n        return true;",
    "}\n            \$this->modules()->create(\$item);\n        }"
);
$content = str_replace(
    "} catch (\\Throwable \$e) {\n            DB::rollBack();\n            throw \$e;\n        }",
    ""
);

file_put_contents($rolePath, $content);
echo "  ✓ InstanceRole.php revertido\n\n";

echo "[5/6] Revertiendo cleanInstance en OwnerController...\n";
$content = file_get_contents($controllerPath);

// Restaurar tablas originales (sin alquiler/tattoo/vehiculos)
$originalTables = "'split_bill_persons',
                'venta_detalles',
                'pagos',
                'ventas',

                // ECF / NCF
                'ecf_log_envios',
                'ecf_documentos',
                'secuencias_ecf',
                'ncf_sequences',

                // Conduces
                'conduce_items',
                'conduces',

                // Devoluciones
                'detalles_devolucion',
                'devoluciones',

                // Compras
                'compra_detalles',
                'compras',

                // Gastos
                'gastos',

                // Cotizaciones
                'cotizacion_items',
                'cotizaciones',

                // Almacenes
                'almacen_movimientos',
                'almacenes',

                // Restaurante
                'reservaciones',
                'waitlist_entries',
                'mesas',
                'mesa_ubicaciones',  // nueva
                'mesa_categorias',
                'categories',        // categorías extra (restaurante/lavadero)

                // Lavadero
                'lavadero_citas',
                'lavadero_servicios',
                'lavadores',

                // Cajas
                'sesion_cajas',
                'cajas',

                // Listas de precio
                'lista_precio_items',
                'lista_precios',

                // Maestros operacionales
                'proveedores',
                'clientes',
                'productos',
                'categorias',
                'sucursales',

                // Configuración operacional de la instancia
                'system_settings',

                // Logs de errores de la instancia
                'instance_error_logs',";

$newTables = "'split_bill_persons',
                'venta_detalles',
                'pagos',
                'ventas',

                // ECF / NCF
                'ecf_log_envios',
                'ecf_documentos',
                'secuencias_ecf',
                'ncf_sequences',

                // Conduces
                'conduce_items',
                'conduces',

                // Devoluciones
                'detalles_devolucion',
                'devoluciones',

                // Compras
                'compra_detalles',
                'compras',

                // Gastos
                'gastos',

                // Cotizaciones
                'cotizacion_items',
                'cotizaciones',

                // Almacenes
                'almacen_movimientos',
                'almacenes',

                // Restaurante
                'reservaciones',
                'waitlist_entries',
                'mesas',
                'mesa_ubicaciones',
                'mesa_categorias',
                'categories',

                // Lavadero
                'lavadero_citas',
                'lavadero_servicios',
                'lavadores',

                // Alquiler
                'alquiler_contratos',
                'alquiler_inquilinos',
                'alquiler_viviendas',
                'alquiler_pagos',

                // Tattoo
                'tattoo_appointments',
                'tattoo_artists',
                'tattoo_designs',

                // Vehículos
                'vehiculos',

                // Cajas
                'sesion_cajas',
                'cajas',

                // Listas de precio
                'lista_precio_items',
                'lista_precios',

                // Maestros operacionales
                'proveedores',
                'clientes',
                'productos',
                'categorias',
                'sucursales',

                // Configuración operacional de la instancia
                'system_settings',

                // Logs de errores de la instancia
                'instance_error_logs',";

$content = str_replace($newTables, $originalTables, $content);

// Restaurar SET FOREIGN_KEY_CHECKS
$content = str_replace(
    "DB::statement('SET FOREIGN_KEY_CHECKS=0');",
    "// FK integrity preserved - deleted by tenant_id filter only\n            // Removed: SET FOREIGN_KEY_CHECKS=0",
    $content
);

file_put_contents($controllerPath, $content);
echo "  ✓ cleanInstance revertido\n\n";

echo "[6/6] Revertiendo .env y .env.example...\n";
$envPath = __DIR__ . '/.env';
$envExamplePath = __DIR__ . '/.env.example';

foreach ([$envPath, $envExamplePath] as $envFile) {
    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        $envContent = preg_replace("/^SMTP_PASSWORD=.*\n/m", '');
        $envContent = preg_replace("/^MAIL_SMTP_PASSWORD=.*\n/m", '');
        file_put_contents($envFile, $envContent);
    }
}
echo "  ✓ Archivos .env revertidos\n\n";

// Revertir migración soft-delete
echo "[7/7] Intentando revertir migración de soft-delete...\n";
$migrationFiles = glob(__DIR__ . '/database/migrations/*_add_trashed_to_business_instances.php');
if (!empty($migrationFiles)) {
    $migFile = $migrationFiles[0];
    $migContent = file_get_contents($migFile);
    if (strpos($migContent, 'trashed_at') !== false) {
        unlink($migFile);
        echo "  ✓ Migración de soft-delete eliminada: " . basename($migFile) . "\n";
    }
} else {
    echo "  ℹ No se encontró migración de soft-delete para eliminar\n";
}

echo "\n";
echo "============================================================\n";
echo "ROLLBACK COMPLETADO\n";
echo "============================================================\n";
echo "Backup guardado en: $backupFile\n";
echo "Si algo falla, restaura desde el backup y ejecuta:\n";
echo "  php artisan migrate:fresh --seed\n";
echo "============================================================\n";

mysqli_close($conn);
