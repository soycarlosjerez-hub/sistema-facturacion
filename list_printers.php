<?php
require __DIR__ . '/vendor/autoload.php';
$impresoras = App\Models\Impresora::all();
foreach ($impresoras as $imp) {
    echo "ID: " . $imp->id . " | Nombre: " . $imp->nombre . " | Tipo: " . $imp->tipo_conexion . " | Driver: " . $imp->driver . " | Papel: " . $imp->papel_tamano . " | Activo: " . ($imp->activo ? 'yes' : 'no') . "\n";
}