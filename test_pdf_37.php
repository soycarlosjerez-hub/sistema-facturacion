<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotizacion;

$cotizacion = Cotizacion::find(37);
if($cotizacion) {
    echo 'Found cotizacion: ' . $cotizacion->numero . PHP_EOL;
    echo 'Estado: ' . $cotizacion->estado . PHP_EOL;
    $cotizacion->load(['cliente', 'user', 'items']);
    $pdf = Pdf::loadView('cotizaciones.pdf', compact('cotizacion'));
    echo 'PDF generated successfully. Size: ' . strlen($pdf->output()) . ' bytes' . PHP_EOL;
} else {
    echo 'Cotizacion 37 not found' . PHP_EOL;
}