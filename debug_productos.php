<?php

$sql = file_get_contents("/var/www/html/sistema-facturacion/storage/app/backups/backup_facturacion_db_20260914_163058.sql");

// Find INSERT for productos
$pos = strpos($sql, "INSERT INTO `productos`");
$chunk = substr($sql, $pos, 50000);
$end = strpos($chunk, ");");
$insert = substr($chunk, 0, $end + 2);

// Extract first few tuples
if (preg_match("/VALUES\s+(.+)/s", $insert, $m)) {
    $values = $m[1];
    $tuples = [];
    $depth = 0;
    $current = '';
    $inQuote = false;
    $quoteChar = '';
    
    for ($i = 0; $i < strlen($values) && count($tuples) < 5; $i++) {
        $c = $values[$i];
        if (($c === "'" || $c === '"') && !$inQuote) {
            $inQuote = true;
            $quoteChar = $c;
            $current .= $c;
        } elseif ($c === $quoteChar && $inQuote) {
            $inQuote = false;
            $quoteChar = '';
            $current .= $c;
        } elseif ($c === '(' && !$inQuote) {
            if ($depth === 0) $current = '';
            else $current .= $c;
            $depth++;
        } elseif ($c === ')' && !$inQuote) {
            $depth--;
            if ($depth === 0) {
                $tuples[] = $current;
                $current = '';
            } else {
                $current .= $c;
            }
        } else {
            $current .= $c;
        }
    }
    
    foreach ($tuples as $j => $t) {
        echo "Tuple $j:\n";
        $fields = [];
        $cur = '';
        $inQ = false;
        $qChar = '';
        for ($k = 0; $k < strlen($t); $k++) {
            $c = $t[$k];
            if (($c === "'" || $c === '"') && !$inQ) {
                $inQ = true;
                $qChar = $c;
                $cur .= $c;
            } elseif ($c === $qChar && $inQ) {
                $inQ = false;
                $qChar = '';
                $cur .= $c;
            } elseif ($c === ',' && !$inQ) {
                $fields[] = trim($cur);
                $cur = '';
            } else {
                $cur .= $c;
            }
        }
        if ($cur) $fields[] = trim($cur);
        
        echo "  Fields: " . count($fields) . "\n";
        foreach ($fields as $idx => $f) {
            if ($idx < 10 || $idx == 20 || $idx == 31 || $idx == 1) {
                echo "  [$idx] $f\n";
            }
        }
        echo "\n";
    }
}