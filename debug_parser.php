<?php

$values = "(5,1,'test',NULL,1,'#6366f1','bi-grid',0,NULL,NULL,'2026-07-01 13:10:13','2026-07-01 13:10:13'),(6,2,'ADICIONALES',NULL,1,'#6366f1','bi-grid',0,NULL,NULL,'2026-07-02 15:13:03','2026-07-02 15:13:03')";

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

$tuples = parseTuples($values);
echo "Tuples found: " . count($tuples) . "\n";
foreach ($tuples as $i => $t) {
    echo "Tuple $i: " . $t['count'] . " fields\n";
    foreach ($t['fields'] as $j => $f) {
        echo "  [$j] $f\n";
    }
}