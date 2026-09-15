<?php

$values = "(21,1,'test',NULL,NULL,NULL,1000.00,0.00,'Unidad',18.00,0,0,0,0,1,0,1,NULL,'2026-07-01 13:10:22','2026-07-01 13:10:22',NULL,NULL,'producto',0,NULL,NULL,0,NULL,NULL,0,NULL,'accesorio',0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'producto','fisico','todos',NULL,NULL,0.00,0,30,1,NULL)";

function parseFields($tupleStr) {
    $fields = [];
    $cur = "";
    $inQ = false;
    $qChar = "";
    for ($k = 0; $k < strlen($tupleStr); $k++) {
        $c = $tupleStr[$k];
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
    return $fields;
}

$fields = parseFields($values);
echo "Fields count: " . count($fields) . "\n";
foreach ($fields as $idx => $f) {
    if ($idx < 10 || $idx == 20 || $idx == 31 || $idx == 1 || $idx == 6 || $idx == 7 || $idx == 8 || $idx == 9 || $idx == 10) {
        echo "  [$idx] $f\n";
    }
}

$index_map = [
    0 => 'id', 1 => 'tenant_id', 2 => 'nombre', 3 => 'codigo_barras', 6 => 'precio', 7 => 'precio_compra',
    8 => 'unidad_medida', 9 => 'itbis_porcentaje', 10 => 'stock', 12 => 'ventas_count', 13 => 'stock_minimo',
    14 => 'activo', 17 => 'imagen', 18 => 'created_at', 19 => 'updated_at', 20 => 'categoria_id',
    31 => 'especializacion', 32 => 'vendible_imei', 33 => 'requiere_imei', 34 => 'marca', 35 => 'modelo',
    36 => 'capacidad_toneladas', 37 => 'capacidad_btu', 38 => 'tipo_equipo', 39 => 'eficiencia_seer',
    40 => 'gas_refrigerante', 41 => 'voltaje', 42 => 'peso_kg', 43 => 'dimensiones', 44 => 'categoria_clima',
    48 => 'almacenamiento_gb', 49 => 'color', 50 => 'precio_servicio', 51 => 'duracion_servicio_horas',
    52 => 'garantia_dias',
];

echo "\nMapped values:\n";
foreach ($index_map as $backupIdx => $currentField) {
    if (isset($fields[$backupIdx])) {
        echo "  $currentField (backup[$backupIdx]) = $fields[$backupIdx]\n";
    }
}