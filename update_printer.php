<?php
// Tinker script to update printer
// Run with: php artisan tinker --execute=file_update

require __DIR__ . '/vendor/autoload.php';

// Simple approach - update via direct SQL feel
// But let's try using the model

// We'll use a different approach - just update via Eloquent
$impresora = new App\Models\Impresora();
$impresora->id = 1;
$impresora->nombre = 'USB002 Térmica';
$impresora->tipo_conexion = 'local';
$impresora->driver = 'escpos';
$impresora->papel_tamano = '58mm';
$impresora->caracteres_por_linea = 32;
$impresora->ruta_compartida = 'LPT1';
$impresora->activo = true;
$impresora->descripcion = 'Impresora térmica USB002 para recibos';
$impresora->orden = 1;

// Need to find existing first or just force update
// Let's try updating by ID
try {
    // First find existing
    $existing = App\Models\Impresora::find(1);
    if ($existing) {
        $existing->nombre = 'USB002 Térmica';
        $existing->tipo_conexion = 'local';
        $existing->driver = 'escpos';
        $existing->papel_tamano = '58mm';
        $existing->caracteres_por_linea = 32;
        $existing->ruta_compartida = 'LPT1';
        $existing->activo = true;
        $existing->descripcion = 'Impresora térmica USB002 para recibos';
        $existing->orden = 1;
        $existing->save();
        echo "Actualizada la impresora existente ID=1\n";
    } else {
        // Create new if not exists
        $existing = $impresora->save();
        echo "Creada nueva impresora\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}