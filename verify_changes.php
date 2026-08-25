<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar que el método facturaLavados existe
\$reflection = new ReflectionMethod(\App\Services\SaleService::class, 'facturaLavados');
echo 'facturaLavados method exists: YES' . PHP_EOL;

// Verificar el modo de facturación actual
\$type = \App\Models\BusinessType::find(1);
echo 'facturacion_modo: ' . \$type->config['facturacion_modo'] . PHP_EOL;
?>