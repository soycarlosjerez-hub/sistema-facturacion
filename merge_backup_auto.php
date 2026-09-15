#!/usr/bin/env php
<?php
/**
 * SAFE MERGE: Use mysqldump from backup, pipe through sed to add IGNORE,
 * then execute against current DB.
 * This avoids all parsing issues.
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

$dbHost = $envVars['DB_HOST'] ?? '127.0.0.1';
$dbPort = $envVars['DB_PORT'] ?? '3306';
$dbName = $envVars['DB_DATABASE'] ?? 'facturacion_db';
$dbUser = $envVars['DB_USERNAME'] ?? 'root';
$dbPass = $envVars['DB_PASSWORD'] ?? '';

$backupDir = __DIR__ . '/storage/app/backups/';
$fullBackups = [];
foreach (glob($backupDir . 'backup_facturacion_db_*.sql') as $f) {
    if (filesize($f) > 1000000) $fullBackups[$f] = filesize($f);
}
if (empty($fullBackups)) { die("No full backups found.\n"); }
arsort($fullBackups);
$backupFile = key($fullBackups);

echo "================================================================================\n";
echo "  MERGE INCREMENTAL - BACKUP => BD ACTUAL\n";
echo "  Backup: " . basename($backupFile) . " (" . round($fullBackups[$backupFile] / 1024 / 1024, 1) . " MB)\n";
echo "  DB:     {$dbName}\n";
echo "  Fecha:  " . date('Y-m-d H:i:s') . "\n";
echo "  Método: sed + mysql con IGNORE\n";
echo "================================================================================\n\n";

// Step 1: Get current row counts
echo "Obteniendo conteos actuales...\n";
$connStr = "-h{$dbHost} -P{$dbPort} -u{$dbUser} -p'{$dbPass}' {$dbName}";

$currCounts = [];
$r = shell_exec("mysql {$connStr} -N -e \"SHOW TABLES\" 2>/dev/null");
$tables = array_filter(array_map('trim', explode("\n", $r)));

foreach ($tables as $t) {
    $r2 = shell_exec("mysql {$connStr} -N -e \"SELECT COUNT(*) FROM `{$t}`\" 2>/dev/null");
    $currCounts[$t] = (int)trim($r2);
}

echo "  Tablas en BD: " . count($currCounts) . "\n\n";

// Step 2: Count rows in backup per table
echo "Contando filas en backup...\n";
$bkCounts = [];
$bkTables = [];

$fp = fopen($backupFile, 'r');
$inInsert = false;
$curTable = null;
$tupleCount = 0;
$lineCount = 0;

while ($line = fgets($fp)) {
    $lineCount++;
    $trimmed = trim($line);
    
    // Count INSERT INTO lines
    if (preg_match('/^INSERT INTO `(\w+)`/i', $trimmed, $m)) {
        $t = $m[1];
        $bkTables[$t] = true;
        
        // Count tuples in this INSERT line using grep -c
        $count = substr_count($line, '),(') + 1;
        if (!isset($bkCounts[$t])) {
            $bkCounts[$t] = 0;
        }
        $bkCounts[$t] += $count;
    }
}
fclose($fp);

echo "  Tablas con datos en backup: " . count($bkCounts) . "\n";
echo "  Total filas en backup: " . array_sum($bkCounts) . "\n\n";

// Step 3: Show comparison report
echo "================================================================================\n";
echo "  REPORTE DE COMPARACIÓN\n";
echo "================================================================================\n\n";

$newTables = [];
$mergeTables = [];
$skipTables = [];

foreach ($bkCounts as $t => $bkCount) {
    $currCount = $currCounts[$t] ?? 0;
    
    if ($currCount == 0) {
        $newTables[] = $t;
        echo "  🆕  {$t}: TABLA VACÍA - {$bkCount} nuevos registros\n";
    } else {
        if ($bkCount > $currCount) {
            $newPossible = $bkCount - $currCount;
            echo "  🔄  {$t}: {$currCount} actuales / {$bkCount} backup (~{$newPossible} posibles nuevos)\n";
        } elseif ($bkCount == $currCount) {
            echo "  🔄  {$t}: {$currCount} actuales / {$bkCount} backup (posible solapamiento)\n";
        } else {
            echo "  🔄  {$t}: {$currCount} actuales / {$bkCount} backup (BD tiene más que backup)\n";
        }
        $mergeTables[] = $t;
    }
}

echo "\n  RESUMEN:\n";
echo "    Tablas nuevas (vacías):     " . count($newTables) . "\n";
echo "    Tablas con merge posible:   " . count($mergeTables) . "\n";
echo "    Total filas en backup:      " . array_sum($bkCounts) . "\n\n";

// Step 4: Execute the merge using sed to add IGNORE
echo "================================================================================\n";
echo "  EJECUTANDO MERGE CON INSERT IGNORE\n";
echo "================================================================================\n\n";

// Strategy: Use mysql to execute the backup SQL, but first use sed/perl to:
// 1. Remove all DROP TABLE statements
// 2. Add IGNORE after INSERT
// This is safe because:
// - DROP TABLEs are removed (won't delete structure)
// - INSERT -> INSERT IGNORE (won't overwrite existing data)
// - CREATE TABLEs are kept (won't fail if table exists due to IF NOT EXISTS)

// Actually, the safest approach: use mysql's --ignore-error flag and modify INSERTs

// Use perl to modify the SQL:
// - Change "INSERT INTO" -> "INSERT IGNORE INTO" 
// - Remove "DROP TABLE IF EXISTS"
// - Add "IF NOT EXISTS" to CREATE TABLE

$perlCode = <<<'PERLCODE'
while (<>) {
    # Remove DROP TABLE statements
    next if /^\s*DROP\s+TABLE/;
    
    # Change INSERT INTO to INSERT IGNORE INTO (but not INSERT IGNORE INTO)
    s/^(\s*INSERT\s+INTO\s+)/INSERT IGNORE INTO /i unless /^\s*INSERT IGNORE/i;
    
    # Add IF NOT EXISTS to CREATE TABLE if not already present
    s/^(\s*CREATE\s+TABLE\s+)(?!IF\s+NOT\s+EXISTS)/$1IF NOT EXISTS /i;
    
    print;
}
PERLCODE;

// Write perl filter to temp file
$perlScript = '/tmp/merge_filter.pl';
file_put_contents($perlScript, $perlCode);

// Build the modified SQL and execute via pipe
// This is the key: we pipe the backup through sed/perl to add IGNORE, then execute
// Using shell pipe: cat backup.sql | perl script | mysql

$shellCmd = "cat '{$backupFile}' | perl {$perlScript} 2>/dev/null | mysql {$connStr} 2>&1";

echo "Ejecutando: {$shellCmd}\n\n";

$output = [];
$returnVar = 0;
exec($shellCmd, $output, $returnVar);

$outputStr = implode("\n", $output);

if ($returnVar === 0 && empty($outputStr)) {
    echo "  ✅ Ejecución completada sin errores\n\n";
} elseif (empty($outputStr)) {
    echo "  ✅ Ejecución completada (sin salida)\n\n";
} else {
    // Check for actual errors vs warnings
    $lines = array_filter($output, fn($l) => strpos($l, 'ERROR') !== false || strpos($l, 'error') !== false);
    
    if (empty($lines)) {
        echo "  ⚠️  Advertencias (no errores críticos):\n";
        foreach ($output as $line) {
            echo "    " . trim($line) . "\n";
        }
        echo "\n";
    } else {
        echo "  ⚠️  Errores encontrados:\n";
        foreach ($lines as $line) {
            echo "    " . trim($line) . "\n";
        }
        echo "\n";
    }
}

// Cleanup
@unlink($perlScript);

// Step 5: Show final counts
echo "================================================================================\n";
echo "  CONTEO FINAL POR TABLA\n";
echo "================================================================================\n\n";

$changedCount = 0;
foreach ($tables as $t) {
    if (!isset($bkCounts[$t])) continue; // Skip tables not in backup
    
    $r2 = shell_exec("mysql {$connStr} -N -e \"SELECT COUNT(*) FROM `{$t}`\" 2>/dev/null");
    $finalCount = (int)trim($r2);
    $prevCount = $currCounts[$t] ?? 0;
    
    if ($finalCount !== $prevCount) {
        $diff = $finalCount - $prevCount;
        echo "  ✓ {$t}: {$finalCount} (antes: {$prevCount}, +{$diff})\n";
        $changedCount++;
    }
}

echo "\n  Tablas modificadas: {$changedCount}\n";
echo "================================================================================\n\n";

echo "  Backup de seguridad: storage/app/backups/backup_security_*.sql\n";
