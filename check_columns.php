<?php

$sql = file_get_contents('/var/www/html/sistema-facturacion/storage/app/backups/backup_facturacion_db_20260914_163058.sql');

function getFirstTuple($sql, $table) {
    $pos = strpos($sql, "INSERT INTO `" . $table . "`");
    if ($pos === false) return "NO ENCONTRADO";
    $chunk = substr($sql, $pos, 20000);
    $end = strpos($chunk, ");");
    if ($end === false) return "ERROR";
    $insert = substr($chunk, 0, $end + 2);
    
    if (preg_match("/VALUES\s+\((.+?)\)/s", $insert, $m)) {
        return $m[1];
    }
    return "ERROR PARSE";
}

$tables = ["productos", "clientes", "ventas", "venta_detalles", "almacen_movimientos", "almacenes", "categorias", "proveedores", "users"];
foreach ($tables as $t) {
    $tuple = getFirstTuple($sql, $t);
    if (is_string($tuple) && strlen($tuple) > 100) {
        $count = 1;
        $inQuote = false;
        $quoteChar = "";
        for ($i = 0; $i < strlen($tuple); $i++) {
            $c = $tuple[$i];
            if (($c === "'" || $c === '"') && !$inQuote) {
                $inQuote = true;
                $quoteChar = $c;
            } elseif ($c === $quoteChar && $inQuote) {
                $inQuote = false;
                $quoteChar = "";
            } elseif ($c === "," && !$inQuote) {
                $count++;
            }
        }
        echo "$t: $count columnas en backup\n";
    } else {
        echo "$t: $tuple\n";
    }
}