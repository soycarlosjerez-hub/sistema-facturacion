<?php

$sql = file_get_contents("/var/www/html/sistema-facturacion/storage/app/backups/backup_facturacion_db_20260914_163058.sql");
$pos = strpos($sql, "INSERT INTO `productos`");
$chunk = substr($sql, $pos, 300000);
$end = strpos($chunk, ");");
$insert = substr($chunk, 0, $end + 2);

if (preg_match("/VALUES\s+(.+)/s", $insert, $m)) {
    $values = $m[1];
    echo "Values length: " . strlen($values) . "\n";
    
    $firstParen = strpos($values, "(");
    $secondParen = strpos($values, ")");
    if ($firstParen !== false && $secondParen !== false) {
        $firstTuple = substr($values, $firstParen + 1, $secondParen - $firstParen - 1);
        echo "First tuple length: " . strlen($firstTuple) . "\n";
        
        $fields = [];
        $cur = "";
        $inQ = false;
        $qChar = "";
        for ($k = 0; $k < strlen($firstTuple); $k++) {
            $c = $firstTuple[$k];
            if (($c === "'" || $c === '"') && !$inQ) {
                $inQ = true;
                $qChar = $c;
                $cur .= $c;
            } elseif ($c === $qChar && $inQ) {
                $inQ = false;
                $qChar = "";
                $cur .= $c;
            } elseif ($c === "," && !$inQ) {
                $fields[] = trim($cur);
                $cur = "";
            } else {
                $cur .= $c;
            }
        }
        if ($cur) $fields[] = trim($cur);
        
        echo "Fields: " . count($fields) . "\n";
        foreach ($fields as $idx => $f) {
            if ($idx < 10 || $idx == 20 || $idx == 31 || $idx == 1 || $idx == 6 || $idx == 7 || $idx == 8 || $idx == 9) {
                echo "  [$idx] $f\n";
            }
        }
    }
}