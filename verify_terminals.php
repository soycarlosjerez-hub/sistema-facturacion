<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$terminales = \App\Models\Terminal::all();
echo "Terminales existentes:\n";
foreach ($terminales as $t) {
    echo "  ID: " . $t->id . " | Codigo: " . $t->codigo . " | Nombre: " . $t->nombre . " | Tenant: " . $t->tenant_id . "\n";
}
?>