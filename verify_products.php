<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = \App\Models\Producto::where('tenant_id', 3)->get()->take(5);
foreach ($products as $p) {
    echo 'Producto: ' . $p->nombre . PHP_EOL;
    echo '  Imagen: ' . $p->imagen . PHP_EOL;
    echo '  Tiene imagen: ' . ($p->getTieneImagenAttribute() ? 'Sí' : 'No') . PHP_EOL;
    echo '  Imagen URL: ' . $p->getImagenUrlAttribute() . PHP_EOL;
    echo PHP_EOL;
}
?>