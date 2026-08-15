<?php
require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel app minimal
$app = new Illuminate\Foundation\Application();
$app->singleton(Illuminate\Contracts\Http\Kernel::class, Illuminate\Foundation\Http\Kernel::class);

// Get the printer and update it
$impresora = App\Models\Impresora::find(1);

$impresora->nombre = 'USB002 Térmica';
$impresora->tipo_conexion = 'local';
$impresora->driver = 'escpos';
$impresora->papel_tamano = '58mm';
$impresora->caracteres_por_linea = 32;
$impresora->ruta_compartida = 'LPT1';
$impresora->activo = true;
$impresora->descripcion = 'Impresora térmica USB002 para recibos';
$impresora->orden = 1;

$impresora->save();

echo "Impresora actualizada:\n";
echo "  Nombre: " . $impresora->nombre . "\n";
echo "  Tipo: " . $impresora->tipo_conexion . "\n";
echo "  Driver: " . $impresora->driver . "\n";
echo "  Papel: " . $impresora->papel_tamano . "\n";
echo "  Caracteres/línea: " . $impresora->caracteres_por_linea . "\n";
echo "  Ruta: " . $impresora->ruta_compartida . "\n";
echo "  Activo: " . ($impresora->activo ? 'Sí' : 'No') . "\n";