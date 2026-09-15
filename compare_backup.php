<?php
/**
 * Compare backup vs current database - ANALYSIS ONLY (no modifications)
 */

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

function logMsg($msg) { echo $msg . PHP_EOL; }
function getDsn($c) { return "mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4"; }

logMsg("================================================================================");
logMsg("  COMPARACIÓN BACKUP vs BD ACTUAL - REPORTE ANALÍTICO");
logMsg("  Backup: " . basename($backupFile));
logMsg("  Fecha:  " . date('Y-m-d H:i:s'));
logMsg("================================================================================");
logMsg("");

// Connect
try {
    $pdo = new PDO(getDsn($dbConfig), $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    logMsg("✓ Conexión a BD establecida.");
} catch (PDOException $e) {
    logMsg("✗ ERROR: " . $e->getMessage());
    exit(1);
}

// Extract INSERT statements from backup
logMsg("📖 Parseando backup...");
$tablesInBackup = []; // table => ['columns' => [...], 'values' => '...', 'count' => N]

$handle = fopen($backupFile, 'r');
$currentTable = null;
$currentColumns = null;
$sqlBuffer = '';
$inInsert = false;
$totalLines = 0;
$totalInsertRows = 0;

while ($line = fgets($handle)) {
    $totalLines++;
    $line = rtrim($line);
    if (empty(trim($line))) continue;
    
    $trimmed = trim($line);
    
    // Skip non-INSERT statements
    if (str_starts_with($trimmed, '/*!') || str_starts_with($trimmed, 'SET')
        || str_starts_with($trimmed, 'COMMIT') || str_starts_with($trimmed, 'LOCK')
        || str_starts_with($trimmed, 'UNLOCK') || str_starts_with($trimmed, 'DROP')
        || str_starts_with($trimmed, '--') || str_starts_with($trimmed, 'CREATE')
        || str_starts_with($trimmed, '/*')) {
        continue;
    }
    
    // Match INSERT statement
    if (preg_match('/^INSERT INTO `(\w+)`\s*(?:\(([^)]+)\))?\s*VALUES\s*(\(.+)$/s', $trimmed, $m)) {
        $table = $m[1];
        $colsStr = $m[2] ?? null;
        $valsStr = $m[3];
        
        if ($colsStr) {
            $columns = array_map(fn($c) => trim(trim($c), '`'), explode(',', $colsStr));
        } else {
            // Get columns from DB
            if (!isset($tablesInBackup[$table]['db_columns'])) {
                try {
                    $result = $pdo->query("DESCRIBE `{$table}`");
                    $tablesInBackup[$table]['db_columns'] = [];
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $tablesInBackup[$table]['db_columns'][] = $row['Field'];
                    }
                } catch (PDOException $e) {
                    $tablesInBackup[$table]['db_columns'] = [];
                }
            }
            $columns = $tablesInBackup[$table]['db_columns'] ?? [];
            $valsStr = rtrim($valsStr, ');');
            $count = 1 + substr_count($valsStr, "),(");
            $tablesInBackup[$table] = [
                'columns' => $columns,
                'values' => $valsStr . ');',
                'count' => $count,
            ];
            $totalInsertRows += $count;
            continue;
        }
        
        if (str_ends_with($trimmed, ');')) {
            $valsStr = rtrim($valsStr, ');');
            $count = 1 + substr_count($valsStr, "),(");
            $tablesInBackup[$table] = [
                'columns' => $columns,
                'values' => $valsStr . ');',
                'count' => $count,
            ];
            $totalInsertRows += $count;
        } else {
            $inInsert = true;
            $currentTable = $table;
            $currentColumns = $columns;
            $sqlBuffer = rtrim($valsStr, ');') . ' ';
        }
        continue;
    }
    
    // Multi-line continuation
    if ($inInsert) {
        $sqlBuffer .= $line . ' ';
        if (str_ends_with(trim($line), ');')) {
            $sqlBuffer = rtrim($sqlBuffer, ');');
            $count = 1 + substr_count($sqlBuffer, "),(");
            $tablesInBackup[$currentTable] = [
                'columns' => $currentColumns,
                'values' => $sqlBuffer . ');',
                'count' => $count,
            ];
            $totalInsertRows += $count;
            $inInsert = false;
            $currentTable = null;
            $currentColumns = null;
            $sqlBuffer = '';
        }
        continue;
    }
}
fclose($handle);

logMsg("  ✓ Parseado: {$totalLines} líneas, " . count($tablesInBackup) . " tablas con datos, {$totalInsertRows} filas totales");
logMsg("");

// Get all current tables
$result = $pdo->query("SHOW TABLES");
$currentTables = [];
while ($row = $result->fetch(PDO::FETCH_NUM)) {
    $currentTables[] = $row[0];
}

// Get unique keys for each table
$uniqueKeys = [];
foreach ($currentTables as $t) {
    $result = $pdo->query("SHOW INDEX FROM `{$t}`");
    $primary = [];
    $unique = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Non_unique'] == 0) {
            if ($row['Key_name'] === 'PRIMARY') {
                $primary[] = $row['Column_name'];
            } else {
                $unique[$row['Key_name']][] = $row['Column_name'];
            }
        }
    }
    $uniqueKeys[$t] = ['primary' => $primary, 'unique' => $unique];
}

// Analyze each table in backup
logMsg("================================================================================");
logMsg("  REPORTE DE COMPARACIÓN - DATOS EN BACKUP vs BD ACTUAL");
logMsg("================================================================================");
logMsg("");

$report = [];
$stats = ['new_data' => 0, 'merge_data' => 0, 'skip_no_table' => 0, 'skip_no_inserts' => 0, 'total_new_rows' => 0];

foreach ($tablesInBackup as $table => $info) {
    if (!in_array($table, $currentTables)) {
        $report[$table] = ['status' => 'skip_no_table', 'reason' => 'Tabla no existe en BD actual', 'backup_rows' => $info['count']];
        logMsg("  ⏭️  {$table}: No existe en BD actual (saltar) - {$info['count']} registros");
        $stats['skip_no_table']++;
        continue;
    }
    
    $result = $pdo->query("SELECT COUNT(*) as cnt FROM `{$table}`");
    $currentCount = (int)$result->fetch()['cnt'];
    
    $report[$table] = [
        'status' => '',
        'current_rows' => $currentCount,
        'backup_rows' => $info['count'],
    ];
    
    if ($currentCount == 0) {
        $report[$table]['status'] = 'new_data';
        $report[$table]['action'] = 'INSERT ALL (tabla vacía)';
        logMsg("  🆕  {$table}: TABLA VACÍA - {$info['count']} nuevos registros a INSERTAR");
        $stats['new_data']++;
        $stats['total_new_rows'] += $info['count'];
    } else {
        $report[$table]['status'] = 'merge_data';
        
        // Check for primary key overlap
        if (!empty($uniqueKeys[$table]['primary'])) {
            // Sample a few PKs from backup to see if they already exist
            $pkCols = $uniqueKeys[$table]['primary'];
            $sampleIds = [];
            
            // Try to extract PK values from the backup values
            if (!empty($info['columns'])) {
                $pkIndices = [];
                foreach ($pkCols as $pkCol) {
                    if (($idx = array_search($pkCol, $info['columns'])) !== false) {
                        $pkIndices[] = $idx;
                    }
                }
                
                if (!empty($pkIndices)) {
                    // Extract first 5 tuples to check
                    $valsPart = rtrim($info['values'], ');');
                    preg_match_all('/\(([^()]+)\)/', $valsPart, $tuples);
                    $checkCount = min(5, count($tuples[0]));
                    
                    foreach (range(0, $checkCount - 1) as $i) {
                        $pkValues = [];
                        foreach ($pkIndices as $idx) {
                            if (isset($tuples[1][$i])) {
                                $val = $tuples[1][$i];
                                $parts = explode(',', $val);
                                if (isset($parts[$idx])) {
                                    $val = trim($parts[$idx], " '\"");
                                    $pkValues[] = $val;
                                }
                            }
                        }
                        if (!empty($pkValues)) {
                            $sampleIds[] = implode(',', $pkValues);
                        }
                    }
                    
                    if (!empty($sampleIds)) {
                        $placeholders = implode(',', array_fill(0, count($sampleIds), '?'));
                        $pkColStr = '`' . implode('`, `', $pkCols) . '`';
                        
                        // Build query for checking if IDs exist
                        // This is approximate - actual merge needs per-row check
                        try {
                            $checkSql = "SELECT COUNT(*) as cnt FROM `{$table}` WHERE ({$pkColStr}) IN ({$placeholders})";
                            $stmt = $pdo->prepare($checkSql);
                            $stmt->execute($sampleIds);
                            $existingIds = (int)$stmt->fetch()['cnt'];
                            
                            $report[$table]['sample_checked'] = count($sampleIds);
                            $report[$table]['existing_in_current'] = $existingIds;
                            $report[$table]['potentially_new'] = count($sampleIds) - $existingIds;
                            
                            if ($existingIds == count($sampleIds)) {
                                $report[$table]['merge_note'] = 'IDs ya existen (INSERT IGNORE los omitirá)';
                            } elseif ($existingIds > 0) {
                                $report[$table]['merge_note'] = 'Parcialmente solapado: ' . $existingIds . '/' . count($sampleIds) . ' IDs existentes';
                            } else {
                                $report[$table]['merge_note'] = 'Sin solapamiento detectado en muestra';
                            }
                        } catch (PDOException $e) {
                            $report[$table]['merge_note'] = 'No se pudo verificar solapamiento';
                        }
                    }
                }
            }
        }
        
        logMsg("  🔄  {$table}: {$currentCount} existentes, {$info['count']} en backup (INSERT IGNORE - duplicados omitidos)");
        if (isset($report[$table]['merge_note'])) {
            logMsg("       └─ " . $report[$table]['merge_note']);
        }
        $stats['merge_data']++;
    }
}

logMsg("");
logMsg("================================================================================");
logMsg("  RESUMEN");
logMsg("================================================================================");
logMsg("");
logMsg("  Tablas en backup con INSERTs: " . count($tablesInBackup));
logMsg("  Tablas sin INSERTs (solo estructura): " . $stats['skip_no_inserts']);
logMsg("  Tablas que NO existen en BD actual:  {$stats['skip_no_table']}");
logMsg("  Tablas vacías (INSERT todo):         {$stats['new_data']}");
logMsg("  Tablas con datos existentes:         {$stats['merge_data']}");
logMsg("  Filas en backup (total):             {$totalInsertRows}");
logMsg("  Filas potencialmente nuevas:         ~{$stats['total_new_rows']} (tabla 0)");
logMsg("");

// Detailed table list
logMsg("================================================================================");
logMsg("  LISTADO DETALLADO POR TABLA");
logMsg("================================================================================");
logMsg("");

foreach ($report as $table => $info) {
    if ($info['status'] === 'skip_no_table') {
        logMsg("  ⏭️  {$table}: NO EXISTE (saltar) - {$info['backup_rows']} registros en backup");
    } elseif ($info['status'] === 'new_data') {
        logMsg("  🆕  {$table}: INSERT ALL - {$info['backup_rows']} registros nuevos");
    } elseif ($info['status'] === 'merge_data') {
        $note = $info['merge_note'] ?? '';
        if ($note) {
            logMsg("  🔄  {$table}: {$info['current_rows']} actuales / {$info['backup_rows']} backup ({$note})");
        } else {
            logMsg("  🔄  {$table}: {$info['current_rows']} actuales / {$info['backup_rows']} backup");
        }
    }
}

// Save report to file
$reportContent = "=== REPORTE DE COMPARACIÓN BACKUP vs BD ACTUAL ===\n";
$reportContent .= "Backup: " . basename($backupFile) . "\n";
$reportContent .= "Fecha: " . date('Y-m-d H:i:s') . "\n\n";
$reportContent .= "RESUMEN:\n";
$reportContent .= "  Tablas en backup: " . count($tablesInBackup) . "\n";
$reportContent .= "  Tablas sin tabla actual: {$stats['skip_no_table']}\n";
$reportContent .= "  Tablas vacías (INSERT todo): {$stats['new_data']}\n";
$reportContent .= "  Tablas con datos (merge): {$stats['merge_data']}\n";
$reportContent .= "  Filas totales en backup: {$totalInsertRows}\n\n";

foreach ($report as $table => $info) {
    $reportContent .= "  {$table}: {$info['status']} ({$info['current_rows']} actual / {$info['backup_rows']} backup)\n";
}

file_put_contents(__DIR__ . '/storage/app/backups/restore_report.txt', $reportContent);
logMsg("");
logMsg("  Reporte guardado: storage/app/backups/restore_report.txt");
logMsg("");
logMsg("  PARA EJECUTAR EL MERGE, use: php restore_backup_merge.php");
logMsg("================================================================================");
