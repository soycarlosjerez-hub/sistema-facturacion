<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\$type = \App\Models\BusinessType::find(1);
echo 'facturacion_modo: ' . \$type->config['facturacion_modo'] . PHP_EOL;
?>