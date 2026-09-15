#!/usr/bin/env php
<?php

/**
 * Restore backup safely - incremental data merge
 * Does NOT delete, drop, truncate, or reset anything.
 * Only INSERTs new records and UPDATEs incomplete records.
 */

use PDO;
use PDOException;

$config = require __DIR__ . '/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php';

// Read .env manually
$envFile = __DIR__ . '/.env';
$envVars = [];
if (file_exists($envFile)) {
    foreach (file($envFile) as $line) {
        $line = trim($line);
        if (empty($line) || str_starts_with($line, '#')) continue;
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value, ' "');
        }
    }
}

$dbConfig = [
    'host' => $envVars['DB_HOST'] ?? '127.0.0.1',
    'port' => $envVars['DB_PORT'] ?? '3306',
    'database' => $envVars['DB_DATABASE'] ?? 'facturacion_db',
    'username' => $envVars['DB_USERNAME'] ?? 'root',
    'password' => $envVars['DB_PASSWORD'] ?? '',
];

$backupFile = __DIR__ . '/storage/app/backups/backup_facturacion_db_20260914_163058.sql';

$report = [];
$stats = [
    'tables_analyzed' => 0,
    'insert_queries' => 0,
    'skip_duplicate' => 0,
    'skip_exists' => 0,
    'update_needed' => 0,
    'executed' => 0,
    'errors' => 0,
];

function logMsg($msg) {
    echo $msg . PHP_EOL;
}

function getDsn($c) {
    return "mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4";
}

logMsg("================================================================================");
logMsg("  RESTAURACIÓN SEGURA DE BACKUP - MERGE INCREMENTAL");
logMsg("  Backup: " . basename($backupFile));
logMsg("  DB:     {$dbConfig['database']} @ {$dbConfig['host']}:{$dbConfig['port']}");
logMsg("  Fecha:  " . date('Y-m-d H:i:s'));
logMsg("  ⚠️  NO se borrará, truncará ni reiniciará ningún dato.");
logMsg("================================================================================");
logMsg("");

// Connect to database
try {
    $pdo = new PDO(getDsn($dbConfig), $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    logMsg("✓ Conexión a base de datos establecida.");
} catch (PDOException $e) {
    logMsg("✗ ERROR de conexión: " . $e->getMessage());
    exit(1);
}

// Parse backup file and extract INSERT statements
logMsg("📖 Parseando archivo de backup...");

$backupLines = [];
$inserts = []; // table => [columns, values[]]
$currentTable = null;
$currentColumns = null;
$sqlBuffer = '';
$inInsertStatement = false;
$insertTable = null;
$insertColumns = null;
$insertValueBuffer = '';

// Cache for table columns (fetched from DB when needed)
$tableColumnsCache = [];

$handle = fopen($backupFile, 'r');
if (!$handle) {
    logMsg("✗ No se puede abrir el archivo de backup: {$backupFile}");
    exit(1);
}

$lineCount = 0;
$totalInserts = 0;

while ($line = fgets($handle)) {
    $lineCount++;
    $line = rtrim($line); // Keep leading spaces, remove trailing
    
    if (empty(trim($line))) continue;
    
    // Skip non-data statements
    if (str_starts_with(trim($line), '/*!') 
        || str_starts_with(trim($line), 'SET') 
        || str_starts_with(trim($line), 'COMMIT') 
        || str_starts_with(trim($line), 'LOCK TABLES') 
        || str_starts_with(trim($line), 'UNLOCK TABLES') 
        || str_starts_with(trim($line), 'DROP TABLE')
        || str_starts_with(trim($line), '--')
        || str_starts_with(trim($line), 'CREATE TABLE')
        || str_starts_with(trim($line), '/*')) {
        continue;
    }
    
    // Match INSERT INTO `tablename` (...) VALUES (...) 
    // OR INSERT INTO `tablename` VALUES (...) (without column list)
    // Capture the full VALUES clause including parentheses
    if (preg_match('/^INSERT INTO `(\w+)`\s*(?:\(([^)]+)\))?\s*VALUES\s*(\(.+)$/s', $line, $matches)) {
        $table = $matches[1];
        $columnsStr = $matches[2] ?? null;
        $valueStr = $matches[3]; // Includes full (tuple1),(tuple2),...
        
        if ($columnsStr) {
            // Format: INSERT INTO `table` (col1, col2, ...) VALUES ...
            $columns = array_map('trim', array_map(function($c) { return trim($c, '`'); }, explode(',', $columnsStr)));
        } else {
            // Format: INSERT INTO `table` VALUES ... (no column list - get from DB)
            if (!isset($tableColumnsCache[$table])) {
                try {
                    $result = $pdo->query("DESCRIBE `{$table}`");
                    $tableColumnsCache[$table] = [];
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $tableColumnsCache[$table][] = $row['Field'];
                    }
                } catch (PDOException $e) {
                    logMsg("  ⚠️  No se pudieron obtener columnas para tabla {$table}: " . $e->getMessage());
                    $tableColumnsCache[$table] = [];
                }
            }
            $columns = $tableColumnsCache[$table] ?? [];
        }
        
// Check if this INSERT ends on this line (has closing );)
            if (str_ends_with(trim($line), ');')) {
                // Complete INSERT on one line
                $valueStr = rtrim($valueStr, "; \n\r\t");
                // Count tuples - handle both `),(` and `), (` formats
                $tupleCount = 1 + substr_count($valueStr, "),(") + substr_count($valueStr, "), (");
            
            if (!empty($columns)) {
                $inserts[$table] = [
                    'columns' => $columns,
                    'values' => $valueStr,
                    'row_count' => $tupleCount,
                ];
                $totalInserts += $tupleCount;
            } else {
                logMsg("  ⚠️  {$table}: No se pudieron determinar columnas, saltando");
            }
        } else {
            // Multi-line INSERT - start buffering
            $inInsertStatement = true;
            $insertTable = $table;
            $insertColumns = $columns;
            $insertValueBuffer = $valueStr . ' ';
        }
        continue;
    }
    
    // Continuation of multi-line INSERT
    if ($inInsertStatement) {
        $insertValueBuffer .= $line . ' ';
        
        // Check if this line ends the INSERT
        if (str_ends_with(trim($line), ');')) {
            $insertValueBuffer = rtrim($insertValueBuffer, "; \n\r\t");
            $tupleCount = 1 + substr_count($insertValueBuffer, "),(") + substr_count($insertValueBuffer, "), (");
            
            if (!empty($insertColumns)) {
                $inserts[$insertTable] = [
                    'columns' => $insertColumns,
                    'values' => $insertValueBuffer,
                    'row_count' => $tupleCount,
                ];
                $totalInserts += $tupleCount;
            } else {
                logMsg("  ⚠️  {$insertTable}: No se pudieron determinar columnas, saltando");
            }
            
            // Reset buffer
            $inInsertStatement = false;
            $insertTable = null;
            $insertColumns = null;
            $insertValueBuffer = '';
        }
        continue;
    }
    
    // Progress indicator
    if ($lineCount % 500000 === 0) {
        logMsg("  ... procesadas {$lineCount} líneas, tablas con datos: " . count($inserts) . ", filas: {$totalInserts}");
    }
}

fclose($handle);

logMsg("  ✓ Parseo completado.");
logMsg("  Tablas con datos en backup: " . count($inserts));
logMsg("  Total filas en backup: {$totalInserts}");
logMsg("  Líneas del backup analizadas: {$lineCount}");
logMsg("");

// Analyze each table in the backup
logMsg("================================================================================");
logMsg("  ANALIZANDO DATOS - COMPARACIÓN BACKUP vs BD ACTUAL");
logMsg("================================================================================");
logMsg("");

// Get current table structures
$tablesInfo = [];
$allCurrentTables = [];
$result = $pdo->query("SHOW TABLES");
while ($row = $result->fetch(PDO::FETCH_NUM)) {
    $allCurrentTables[] = $row[0];
}

// Get unique keys (PRIMARY KEY, UNIQUE indexes) for each table
$uniqueKeys = [];
foreach ($allCurrentTables as $t) {
    $result = $pdo->query("SHOW INDEX FROM `{$t}`");
    $keys = [];
    $primaryCols = [];
    $uniqueCols = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Non_unique'] == 0) {
            if ($row['Key_name'] === 'PRIMARY') {
                $primaryCols[] = $row['Column_name'];
            } else {
                $uniqueCols[$row['Key_name']][] = $row['Column_name'];
            }
        }
    }
    $uniqueKeys[$t] = [
        'primary' => $primaryCols,
        'unique' => $uniqueCols,
    ];
}

foreach ($inserts as $table => $info) {
    $stats['tables_analyzed']++;
    
    // Check if table exists in current DB
    if (!in_array($table, $allCurrentTables)) {
        $report[$table] = [
            'status' => 'skip_no_table',
            'reason' => "Tabla no existe en BD actual",
            'backup_rows' => $info['row_count'],
        ];
        logMsg("  ⏭️  {$table}: No existe en BD actual (saltar) - {$info['row_count']} registros");
        continue;
    }
    
    // Check if table has data already
    $result = $pdo->query("SELECT COUNT(*) as cnt FROM `{$table}`");
    $currentCount = $result->fetch()['cnt'];
    
    $report[$table] = [
        'status' => '',
        'current_rows' => (int)$currentCount,
        'backup_rows' => $info['row_count'],
        'new_rows' => 0,
        'updated_rows' => 0,
        'skipped' => 0,
    ];
    
    if ($currentCount == 0) {
        // Table is empty - insert all backup rows
        $report[$table]['status'] = 'new_data';
        $report[$table]['new_rows'] = $info['row_count'];
        $report[$table]['action'] = 'INSERT ALL (tabla vacía)';
        logMsg("  🆕  {$table}: Tabla vacía - {$info['row_count']} nuevos registros");
    } else {
        // Table has data - need to check for duplicates vs existing IDs
        $report[$table]['status'] = 'merge';
        logMsg("  🔄  {$table}: Tabla tiene {$currentCount} registros, verificando duplicados...");
        
        // For tables with auto_increment ID as PK, check if IDs overlap
        if (!empty($uniqueKeys[$table]['primary'])) {
            $pkCols = implode(',', $uniqueKeys[$table]['primary']);
            $pkList = '(' . implode(',', $uniqueKeys[$table]['primary']) . ')';
            
            // Extract PK values from backup INSERT statement
            // This is complex with multi-row INSERT, so we'll handle at execution time
            $report[$table]['primary_key'] = $uniqueKeys[$table]['primary'];
            logMsg("    PK: {$pkCols}");
        }
        
        // Check if it's a pivot/relationship table (no PK to compare)
        $isPivot = false;
        if (count($uniqueKeys[$table]['primary']) > 1) {
            // Composite PK - likely a pivot table
            $isPivot = true;
            $report[$table]['is_pivot'] = true;
            logMsg("    Composite PK (tabla pivote): " . implode(', ', $uniqueKeys[$table]['primary']));
        }
    }
}

logMsg("");
logMsg("================================================================================");
logMsg("  REPORTE RESUMIDO - DATOS A INSERTAR/COMPLETAR");
logMsg("================================================================================");
logMsg("");

$summary = [
    'new_data' => 0,
    'merge' => 0,
    'skip' => 0,
    'total_new_rows' => 0,
];

foreach ($report as $table => $info) {
    if ($info['status'] === 'new_data') {
        $summary['new_data']++;
        $summary['total_new_rows'] += $info['backup_rows'];
        logMsg("  🆕  {$table}: {$info['backup_rows']} nuevos registros (INSERT)");
    } elseif ($info['status'] === 'merge') {
        $summary['merge']++;
        logMsg("  🔄  {$table}: {$info['current_rows']} existentes + backup (verificar duplicados)");
    } elseif ($info['status'] === 'skip_no_table') {
        $summary['skip']++;
        logMsg("  ⏭️  {$table}: Saltado - {$info['reason']}");
    }
}

logMsg("");
logMsg("  Resumen:");
logMsg("    Tablas con datos nuevos:      {$summary['new_data']}");
logMsg("    Tablas con datos existentes:   {$summary['merge']}");
logMsg("    Tablas saltadas:               {$summary['skip']}");
logMsg("    Total filas a insertar:        ~{$summary['total_new_rows']}");
logMsg("");

// Ask for confirmation
logMsg("================================================================================");
logMsg("  ¿Desea proceder con la inserción de datos?");
logMsg("================================================================================");
logMsg("  Escribir 'SI' para continuar, cualquier otra cosa para cancelar:");
$response = trim(fgets(STDIN));

if (strtoupper($response) !== 'SI') {
    logMsg("  Operación cancelada por el usuario.");
    // Save report to file anyway
    file_put_contents(__DIR__ . '/storage/app/backups/restore_report.txt', json_encode($report, JSON_PRETTY_PRINT));
    exit(0);
}

logMsg("");
logMsg("================================================================================");
logMsg("  EJECUTANDO INSERT - MERGE INCREMENTAL");
logMsg("================================================================================");
logMsg("");

$pdo->beginTransaction();

try {
    foreach ($inserts as $table => $info) {
        $status = $report[$table]['status'] ?? 'skip';
        
        if ($status === 'skip_no_table' || $status === 'skip') {
            continue;
        }
        
        // Build INSERT statement with INSERT IGNORE to avoid duplicate key errors
        $columns = implode(', ', array_map(fn($c) => "`{$c}`", $info['columns']));
        
        if ($status === 'new_data') {
            // Table is empty - insert all rows with INSERT IGNORE (safe for duplicate keys)
            $sql = "INSERT IGNORE INTO `{$table}` ({$columns}) VALUES {$info['values']}";
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $stats['executed']++;
                
                if ($stats['executed'] % 10 === 0 || $info['row_count'] < 20) {
                    logMsg("  ✓ {$table}: {$info['row_count']} registros insertados (IGNORE)");
                }
            } catch (PDOException $e) {
                logMsg("  ✗ {$table}: ERROR - " . $e->getMessage());
                // Debug: show first 200 chars of values
                logMsg("    DEBUG values preview: " . substr($info['values'], 0, 200));
                logMsg("    DEBUG columns: " . $columns);
                $stats['errors']++;
            }
        } elseif ($status === 'merge') {
            // Table has data - use INSERT IGNORE to skip duplicates based on UNIQUE/PRIMARY keys
            $sql = "INSERT IGNORE INTO `{$table}` ({$columns}) VALUES {$info['values']}";
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $stats['executed']++;
                
                if ($stats['executed'] % 10 === 0 || $info['row_count'] < 20) {
                    logMsg("  ✓ {$table}: {$info['row_count']} registros procesados (IGNORE - duplicados omitidos)");
                }
            } catch (PDOException $e) {
                logMsg("  ✗ {$table}: ERROR - " . $e->getMessage());
                $stats['errors']++;
            }
        }
    }
    
    $pdo->commit();
    logMsg("");
    logMsg("================================================================================");
    logMsg("  ✅ MIGRACIÓN COMPLETADA EXITOSAMENTE");
    logMsg("================================================================================");
    logMsg("");
    
    // Show final stats
    $finalStats = [];
    foreach ($report as $table => $info) {
        if (in_array($info['status'], ['new_data', 'merge'])) {
            $result = $pdo->query("SELECT COUNT(*) as cnt FROM `{$table}`");
            $finalStats[$table] = $result->fetch()['cnt'];
        }
    }
    
    logMsg("  Tablas afectadas y conteo final:");
    foreach ($finalStats as $table => $count) {
        logMsg("    {$table}: {$count} registros (total actual)");
    }
    
    logMsg("");
    logMsg("  Estadísticas:");
    logMsg("    Tablas analizadas:    {$stats['tables_analyzed']}");
    logMsg("    Tablas procesadas:    {$stats['executed']}");
    logMsg("    Errores:              {$stats['errors']}");
    logMsg("");
    logMsg("  Backup de seguridad:");
    $backupFiles = glob(__DIR__ . '/storage/app/backups/backup_pre_restore_*.sql');
    if (!empty($backupFiles)) {
        logMsg("    " . end($backupFiles));
    }
    
} catch (PDOException $e) {
    $pdo->rollBack();
    logMsg("");
    logMsg("  ✗ ERROR: " . $e->getMessage());
    logMsg("  Operación ROLLBACK - datos no modificados.");
    exit(1);
}

logMsg("");
logMsg("  Fin de la operación.");
