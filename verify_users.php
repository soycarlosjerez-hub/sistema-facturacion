<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usuarios = \App\Models\User::all();
echo "Usuarios existentes:\n";
foreach ($usuarios as $u) {
    $terminal = $u->terminal ? 'Terminal: ' . $u->terminal->nombre . ' (' . $u->terminal->codigo . ')' : 'Sin terminal';
    echo "  ID: " . $u->id . " | Email: " . $u->email . " | Nombre: " . $u->nombre . " | " . $terminal . "\n";
}
?>