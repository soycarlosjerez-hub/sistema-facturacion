<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotizacion;

$cotizacion = Cotizacion::first();
if($cotizacion) {
    $cotizacion->load(['cliente', 'user', 'items']);
    $pdf = Pdf::loadView('cotizaciones.pdf', compact('cotizacion'));
    $output = $pdf->output();
    echo 'PDF generated successfully for cotizacion: ' . $cotizacion->numero . PHP_EOL;
    echo 'Size: ' . strlen($output) . ' bytes' . PHP_EOL;
    echo 'First 100 chars: ' . substr($output, 0, 100) . PHP_EOL;
} else {
    echo 'No cotizaciones found' . PHP_EOL;
}